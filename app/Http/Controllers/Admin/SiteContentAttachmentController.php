<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteContentItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteContentAttachmentController extends Controller
{
    private const CHUNK_SIZE = 524288;
    private const MAX_SIZE = 2147483648; // 2 GiB

    public function chunk(Request $request, SiteContentItem $item): JsonResponse
    {
        abort_unless(in_array($item->type, ['news', 'announcement', 'resource'], true), 404);
        $uploadId = (string) $request->header('X-Upload-Id');
        $disk = Storage::disk('local');
        $dir = 'site-content/.uploads';
        $disk->makeDirectory($dir);

        if ($uploadId === '') {
            $data = $request->validate([
                'filename' => ['required', 'string', 'max:255'],
                'size' => ['required', 'integer', 'min:1', 'max:'.self::MAX_SIZE],
            ]);
            $filename = basename($data['filename']);
            abort_unless(strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'pdf', 422, 'Only PDF files are allowed.');

            $uploadId = (string) str()->uuid();
            $disk->put("{$dir}/{$uploadId}.json", json_encode([
                'item_id' => $item->id,
                'filename' => $filename,
                'size' => (int) $data['size'],
                'part_path' => "{$dir}/{$uploadId}.part",
            ], JSON_THROW_ON_ERROR));

            return response()->json(['upload_id' => $uploadId, 'chunk_size' => self::CHUNK_SIZE]);
        }

        $metaPath = "{$dir}/{$uploadId}.json";
        $partPath = "{$dir}/{$uploadId}.part";
        abort_unless($disk->exists($metaPath), 404, 'Upload session not found.');
        $meta = json_decode($disk->get($metaPath), true, 512, JSON_THROW_ON_ERROR);
        abort_unless((int) ($meta['item_id'] ?? 0) === (int) $item->id, 403);

        if ($request->boolean('finalize')) {
            $size = $disk->exists($partPath) ? $disk->size($partPath) : 0;
            abort_unless($size === (int) $meta['size'], 422, 'The upload is incomplete.');

            $absolute = $disk->path($partPath);
            $mime = 'application/octet-stream';
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $detected = finfo_file($finfo, $absolute);
                finfo_close($finfo);
                if ($detected) $mime = $detected;
            }
            abort_unless($mime === 'application/pdf', 422, 'The uploaded file is not a valid PDF.');

            $extension = 'pdf';
            $storedName = (string) str()->uuid().'.'.$extension;
            $publicPath = "site-content/attachments/{$storedName}";
            Storage::disk('public')->makeDirectory('site-content/attachments');
            abort_unless(Storage::disk('public')->put($publicPath, fopen($absolute, 'rb')), 500, 'Unable to store the PDF.');

            if ($item->attachment_path) {
                Storage::disk('public')->delete($item->attachment_path);
            }

            $item->update([
                'attachment_path' => $publicPath,
                'attachment_name' => $meta['filename'],
                'attachment_size' => (int) $meta['size'],
                'attachment_mime' => $mime,
            ]);

            $disk->delete([$metaPath, $partPath]);
            return response()->json([
                'ok' => true,
                'name' => $item->attachment_name,
                'size' => $item->attachment_size,
                'url' => Storage::disk('public')->url($item->attachment_path),
            ]);
        }

        $index = (int) $request->header('X-Chunk-Index', '-1');
        $offset = (int) $request->header('X-Chunk-Offset', '-1');
        $length = (int) $request->header('Content-Length', '0');
        abort_if($index < 0 || $offset < 0 || $length < 1 || $length > self::CHUNK_SIZE, 422, 'Invalid upload chunk.');
        abort_if($offset + $length > (int) $meta['size'], 422, 'Upload chunk exceeds the declared file size.');

        $absolute = $disk->path($partPath);
        $handle = fopen($absolute, 'c+b');
        abort_unless($handle !== false, 500, 'Unable to open upload buffer.');
        if (!flock($handle, LOCK_EX)) {
            fclose($handle);
            abort(423, 'Upload is busy.');
        }
        fseek($handle, $offset);
        $input = fopen('php://input', 'rb');
        $written = $input ? stream_copy_to_stream($input, $handle) : false;
        if ($input) fclose($input);
        fflush($handle);
        flock($handle, LOCK_UN);
        fclose($handle);

        abort_unless($written === $length, 422, 'The upload chunk was incomplete.');
        return response()->json(['ok' => true, 'uploaded' => $offset + $length]);
    }

    public function destroy(SiteContentItem $item): JsonResponse
    {
        abort_unless(in_array($item->type, ['news', 'announcement', 'resource'], true), 404);
        if ($item->attachment_path) Storage::disk('public')->delete($item->attachment_path);
        $item->update([
            'attachment_path' => null,
            'attachment_name' => null,
            'attachment_size' => null,
            'attachment_mime' => null,
        ]);
        return response()->json(['ok' => true]);
    }
}
