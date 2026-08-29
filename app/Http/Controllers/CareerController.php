<?php

namespace App\Http\Controllers;

use App\Models\CareerApplication;
use App\Models\SiteContentItem;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;

class CareerController extends Controller
{
    public function show(): View
    {
        $settings = SystemSetting::query()->pluck('value', 'key')->all();
        $brand = [
            'name' => $settings['company.name'] ?? config('fuelfree.company.name'),
            'logo_path' => $settings['company.logo_path'] ?? null,
            'tagline' => $settings['company.tagline'] ?? config('fuelfree.company.tagline'),
        ];
        $page = SiteContentItem::published()
            ->whereIn('type', ['career','careers','job'])
            ->orderBy('sort_order')->orderBy('title')->get();

        return view('career', compact('brand','page'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateApplication($request);

        unset($data['website']);
        $file = $request->file('cv');
        $path = $file->store('career/cv', 'local');

        CareerApplication::create([
            ...$data,
            'cv_path' => $path,
            'cv_original_name' => $file->getClientOriginalName(),
            'status' => 'new',
        ]);

        return back()->with('career_status', 'Your application has been received. Our career team will review your information and contact you if your profile matches an opportunity.');
    }

    public function chunkUpload(Request $request)
    {
        $uploadsDir = 'private/career/.uploads';
        $disk = \Illuminate\Support\Facades\Storage::disk('local');
        $disk->makeDirectory($uploadsDir);
        $uploadId = (string) $request->header('X-Upload-Id');

        if ($uploadId === '') {
            $data = $request->validate([
                'filename' => ['required','string','max:255'],
                'size' => ['required','integer','min:1','max:52428800'],
            ]);

            $extension = strtolower(pathinfo($data['filename'], PATHINFO_EXTENSION));
            abort_unless(in_array($extension, ['pdf','doc','docx'], true), 422, 'Please upload a PDF, DOC or DOCX file.');
            $uploadId = (string) str()->uuid();
            $metaPath = "{$uploadsDir}/{$uploadId}.json";
            $partPath = "{$uploadsDir}/{$uploadId}.part";

            $disk->put($metaPath, json_encode([
                'filename' => basename($data['filename']),
                'size' => (int) $data['size'],
                'mime_type' => (string) $request->input('mime_type', 'application/octet-stream'),
                'chunk_size' => 524288,
                'part_path' => $partPath,
            ], JSON_THROW_ON_ERROR));

            return response()->json(['upload_id' => $uploadId, 'chunk_size' => 524288]);
        }

        $metaPath = "{$uploadsDir}/{$uploadId}.json";
        $partPath = "{$uploadsDir}/{$uploadId}.part";
        abort_unless($disk->exists($metaPath), 404, 'Upload session not found.');
        $meta = json_decode($disk->get($metaPath), true, 512, JSON_THROW_ON_ERROR);

        if ($request->boolean('finalize')) {
            $currentSize = $disk->exists($partPath) ? $disk->size($partPath) : 0;
            abort_unless($currentSize === (int) $meta['size'], 422, 'The upload is incomplete.');

            $absolute = $disk->path($partPath);
            $uploadedFile = new UploadedFile(
                $absolute,
                $meta['filename'],
                null,
                UPLOAD_ERR_OK,
                true
            );

            $request->files->set('cv', $uploadedFile);
            $data = $this->validateApplication($request);
            unset($data['website']);

            $extension = strtolower(pathinfo($meta['filename'], PATHINFO_EXTENSION));
            $storedName = (string) str()->uuid().($extension ? '.'.$extension : '');
            $finalPath = "private/career/cv/{$storedName}";
            $disk->makeDirectory('private/career/cv');
            $disk->move($partPath, $finalPath);

            CareerApplication::create([
                ...$data,
                'cv_path' => $finalPath,
                'cv_original_name' => $meta['filename'],
                'status' => 'new',
            ]);

            $disk->delete($metaPath);

            return response()->json([
                'ok' => true,
                'redirect' => route('site.career'),
                'message' => 'Your application has been received. Our career team will review your information and contact you if your profile matches an opportunity.',
            ]);
        }

        $index = (int) $request->header('X-Chunk-Index', '-1');
        $offset = (int) $request->header('X-Chunk-Offset', '-1');
        $length = (int) $request->header('Content-Length', '0');
        $chunkSize = (int) $meta['chunk_size'];

        abort_if($index < 0 || $offset < 0 || $length < 1 || $length > $chunkSize, 422, 'Invalid upload chunk.');
        abort_if($offset + $length > (int) $meta['size'], 422, 'Upload chunk exceeds the declared file size.');

        $absolute = $disk->path($partPath);
        $handle = fopen($absolute, 'c+b');
        abort_unless($handle !== false, 500, 'Unable to open upload buffer.');
        if (! flock($handle, LOCK_EX)) {
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

    private function validateApplication(Request $request): array
    {
        return $request->validate([
            'name' => ['required','string','max:120'],
            'email' => ['required','email','max:190'],
            'phone' => ['nullable','string','max:40'],
            'position' => ['nullable','string','max:180'],
            'education' => ['nullable','string','max:255'],
            'experience' => ['nullable','string','max:180'],
            'location' => ['nullable','string','max:180'],
            'message' => ['nullable','string','max:5000'],
            'cv' => ['required', File::types(['pdf','doc','docx'])->max('50mb')],
            'consent' => ['accepted'],
            'website' => ['nullable','string','max:0'],
        ]);
    }
}
