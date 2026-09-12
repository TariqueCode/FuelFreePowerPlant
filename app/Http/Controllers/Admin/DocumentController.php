<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index(Request $request): mixed
    {
        $user = $request->user();
        $folder = null;
        if ($request->filled('folder')) {
            $folder = DocumentFolder::where('id', $request->integer('folder'))->where('user_id', $user->id)->firstOrFail();
        }
        $search = trim((string) $request->query('q', ''));
        $folders = DocumentFolder::where('user_id', $user->id)->where('parent_id', $folder?->id)->withCount(['children', 'documents'])->orderBy('name')->get();
        $documents = Document::where('user_id', $user->id)->where('folder_id', $folder?->id)->when($search !== '', fn ($q) => $q->where('original_name', 'like', "%{$search}%"))->latest()->paginate(20)->withQueryString();
        $allFolders = DocumentFolder::where('user_id', $user->id)->orderBy('name')->get();
        $quotaBytes = (int) SystemSetting::query()->where('key', 'uploads.documents_quota_gb')->value('value') * 1073741824;
        if ($quotaBytes < 1) $quotaBytes = 50 * 1073741824;
        $usedBytes = $this->usedBytes($user->id);
        $availableBytes = max(0, $quotaBytes - $usedBytes);
        $usedPercent = min(100, round(($usedBytes / max(1, $quotaBytes)) * 100, 1));
        return view('admin.documents.index', compact('folder','folders','documents','allFolders','search','quotaBytes','usedBytes','availableBytes','usedPercent'));
    }

    public function storeFolder(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required','string','max:150'], 'parent_id' => ['nullable','integer','exists:document_folders,id']]);
        $parentId = $data['parent_id'] ?? null;
        if ($parentId) DocumentFolder::whereKey($parentId)->where('user_id', $request->user()->id)->firstOrFail();
        DocumentFolder::create(['user_id'=>$request->user()->id,'parent_id'=>$parentId,'name'=>trim($data['name'])]);
        return back()->with('success','Folder created successfully.');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['file'=>['required','file'], 'folder_id'=>['nullable','integer','exists:document_folders,id']]);
        $file=$request->file('file');
        $folderId=$request->integer('folder_id') ?: null;
        if ($folderId) DocumentFolder::whereKey($folderId)->where('user_id',$request->user()->id)->firstOrFail();
        $this->ensureQuotaAvailable($request->user()->id,(int)$file->getSize());
        $extension=$file->getClientOriginalExtension();
        $storedName=(string)Str::uuid().($extension!==''?'.'.$extension:'');
        $path="private/{$request->user()->id}/{$storedName}";
        Storage::disk('local')->putFileAs("private/{$request->user()->id}",$file,$storedName);
        Document::create(['user_id'=>$request->user()->id,'folder_id'=>$folderId,'original_name'=>$file->getClientOriginalName(),'stored_name'=>$storedName,'disk'=>'local','path'=>$path,'mime_type'=>$file->getMimeType() ?: 'application/octet-stream','size'=>(int)$file->getSize(),'extension'=>$extension!==''?$extension:null]);
        return back()->with('success','File uploaded securely.');
    }

    public function chunkUpload(Request $request): mixed
    {
        if (!$request->hasHeader('X-Upload-Id')) {
            $data=$request->validate(['filename'=>['required','string','max:255'],'size'=>['required','integer','min:1'],'folder_id'=>['nullable','integer','exists:document_folders,id']]);
            if (!empty($data['folder_id'])) DocumentFolder::whereKey($data['folder_id'])->where('user_id',$request->user()->id)->firstOrFail();
            $uploadId=(string)Str::uuid();
            Storage::disk('local')->makeDirectory('.uploads');
            $metaPath=".uploads/{$uploadId}.json";
            $partPath=".uploads/{$uploadId}.part";
            $chunkSize=65536;
            Storage::disk('local')->put($metaPath,json_encode(['user_id'=>$request->user()->id,'filename'=>$data['filename'],'size'=>(int)$data['size'],'folder_id'=>$data['folder_id'] ?? null,'chunk_size'=>$chunkSize,'created_at'=>now()->toIso8601String()]));
            return response()->json(['ok'=>true,'upload_id'=>$uploadId,'chunk_size'=>$chunkSize]);
        }
        $uploadId=(string)$request->header('X-Upload-Id');
        abort_unless(preg_match('/^[0-9a-f-]{36}$/i',$uploadId),422,'Invalid upload session.');
        $metaPath=".uploads/{$uploadId}.json";
        $partPath=".uploads/{$uploadId}.part";
        abort_unless(Storage::disk('local')->exists($metaPath),404,'Upload session not found or expired.');
        $meta=json_decode(Storage::disk('local')->get($metaPath),true) ?: [];
        abort_unless((int)($meta['user_id'] ?? 0)===(int)$request->user()->id,403);
        if ($request->boolean('finalize')) {
            $expected=(int)$meta['size'];
            abort_unless(Storage::disk('local')->exists($partPath),422,'Upload data is missing.');
            abort_unless((int)Storage::disk('local')->size($partPath)===$expected,422,'Uploaded data is incomplete.');
            $this->ensureQuotaAvailable($request->user()->id,$expected);
            $extension=pathinfo((string)$meta['filename'],PATHINFO_EXTENSION);
            $storedName=(string)Str::uuid().($extension!==''?'.'.$extension:'');
            $finalPath="private/{$request->user()->id}/{$storedName}";
            Storage::disk('local')->makeDirectory("private/{$request->user()->id}");
            abort_unless(Storage::disk('local')->move($partPath,$finalPath),500,'Unable to finalize uploaded file.');
            $mime='application/octet-stream';
            $absolute=Storage::disk('local')->path($finalPath);
            if (class_exists(\finfo::class)) { $finfo=new \finfo(FILEINFO_MIME_TYPE); $mime=$finfo->file($absolute) ?: $mime; }
            Document::create(['user_id'=>$request->user()->id,'folder_id'=>$meta['folder_id'] ?? null,'original_name'=>$meta['filename'],'stored_name'=>$storedName,'disk'=>'local','path'=>$finalPath,'mime_type'=>$mime,'size'=>$expected,'extension'=>$extension!==''?$extension:null]);
            Storage::disk('local')->delete($metaPath);
            return response()->json(['ok'=>true,'message'=>'File uploaded securely.']);
        }
        $index=(int)$request->header('X-Chunk-Index','-1');
        $offset=(int)$request->header('X-Chunk-Offset','-1');
        $length=(int)($request->header('X-Chunk-Length') ?: $request->header('Content-Length','0'));
        $chunkSize=(int)$meta['chunk_size'];
        abort_if($index<0 || $offset<0 || $length<1 || $length>$chunkSize,422,'Invalid upload chunk.');
        abort_if($offset+$length>(int)$meta['size'],422,'Upload chunk exceeds the declared file size.');
        $absolute=Storage::disk('local')->path($partPath);
        $handle=fopen($absolute,'c+b');
        abort_unless($handle!==false,500,'Unable to open upload buffer.');
        if(!flock($handle,LOCK_EX)){fclose($handle);abort(423,'Upload is busy.');}
        fseek($handle,$offset);
        $input=fopen('php://input','rb');
        $written=$input?stream_copy_to_stream($input,$handle):false;
        if($input)fclose($input);
        fflush($handle);flock($handle,LOCK_UN);fclose($handle);
        abort_unless($written===$length,422,'The upload chunk was incomplete.');
        return response()->json(['ok'=>true,'uploaded'=>$offset+$length]);
    }

    public function renameFolder(Request $request, DocumentFolder $folder): RedirectResponse
    {
        $this->ownFolder($request,$folder);
        $data=$request->validate(['name'=>['required','string','max:150']]);
        $name=trim($data['name']);
        abort_if($name===''||str_contains($name,'/')||str_contains($name,'\\'),422,'Invalid folder name.');
        $folder->update(['name'=>$name]);
        return back()->with('success','Folder renamed successfully.');
    }

    public function moveFolder(Request $request, DocumentFolder $folder): RedirectResponse
    {
        $this->ownFolder($request,$folder);
        $data=$request->validate(['parent_id'=>['nullable','integer','exists:document_folders,id']]);
        $parentId=$data['parent_id']??null;
        if($parentId){$target=DocumentFolder::whereKey($parentId)->where('user_id',$request->user()->id)->firstOrFail();abort_if($target->id===$folder->id||$this->isDescendant($target,$folder),422,'Invalid folder destination.');}
        $folder->update(['parent_id'=>$parentId]);
        return back()->with('success','Folder moved successfully.');
    }

    public function copyFolder(Request $request, DocumentFolder $folder): RedirectResponse
    {
        $this->ownFolder($request,$folder);
        $data=$request->validate(['parent_id'=>['nullable','integer','exists:document_folders,id']]);
        $parentId=$data['parent_id']??null;
        if($parentId)DocumentFolder::whereKey($parentId)->where('user_id',$request->user()->id)->firstOrFail();
        $this->copyFolderTree($folder,$request->user()->id,$parentId);
        return back()->with('success','Folder copied successfully.');
    }

    public function destroyFolder(Request $request, DocumentFolder $folder): RedirectResponse
    {
        $this->ownFolder($request,$folder);
        $this->deleteFolderTree($folder);
        return back()->with('success','Folder deleted permanently.');
    }

    public function rename(Request $request, Document $document): RedirectResponse { $this->ownDocument($request,$document); $data=$request->validate(['name'=>['required','string','max:255']]); $name=trim($data['name']); abort_if($name===''||str_contains($name,'/')||str_contains($name,'\\'),422,'Invalid file name.'); $extension=pathinfo($document->original_name,PATHINFO_EXTENSION); if($extension&&!str_ends_with(strtolower($name),'.'.strtolower($extension)))$name.='.'.$extension; $document->update(['original_name'=>$name]); return back()->with('success','File renamed successfully.'); }
    public function move(Request $request, Document $document): RedirectResponse { $this->ownDocument($request,$document); $data=$request->validate(['folder_id'=>['nullable','integer','exists:document_folders,id']]); $folderId=$data['folder_id']??null; if($folderId)DocumentFolder::whereKey($folderId)->where('user_id',$request->user()->id)->firstOrFail(); $document->update(['folder_id'=>$folderId]); return back()->with('success','File moved successfully.'); }
    public function copy(Request $request, Document $document): RedirectResponse { $this->ownDocument($request,$document); $data=$request->validate(['folder_id'=>['nullable','integer','exists:document_folders,id']]); $folderId=$data['folder_id']??null; if($folderId)DocumentFolder::whereKey($folderId)->where('user_id',$request->user()->id)->firstOrFail(); $this->ensureQuotaAvailable($request->user()->id,(int)$document->size); $disk=Storage::disk($document->disk); $storedName=(string)Str::uuid().($document->extension?'.'.$document->extension:''); $copyPath="private/{$request->user()->id}/{$storedName}"; abort_unless($disk->copy($document->path,$copyPath),500,'Unable to copy the file.'); Document::create(['user_id'=>$request->user()->id,'folder_id'=>$folderId,'original_name'=>pathinfo($document->original_name,PATHINFO_FILENAME).' - Copy'.($document->extension?'.'.$document->extension:''),'stored_name'=>$storedName,'disk'=>$document->disk,'path'=>$copyPath,'mime_type'=>$document->mime_type,'size'=>$document->size,'extension'=>$document->extension]); return back()->with('success','File copied successfully.'); }
    public function download(Request $request, Document $document): mixed { $this->ownDocument($request,$document); abort_unless(Storage::disk($document->disk)->exists($document->path),404); return Storage::disk($document->disk)->download($document->path,$document->original_name); }
    public function share(Request $request, Document $document): RedirectResponse { $this->ownDocument($request,$document); if(!$document->share_token)$document->share_token=bin2hex(random_bytes(32)); $document->share_enabled=true; $document->share_expires_at=now()->addDays(7); $document->save(); return back()->with('success','Secure download link created.'); }
    public function unshare(Request $request, Document $document): RedirectResponse { $this->ownDocument($request,$document); $document->update(['share_enabled'=>false]); return back()->with('success','Download link revoked.'); }
    public function sharedDownload(string $token): mixed { $document=Document::where('share_token',$token)->where('share_enabled',true)->where(fn($q)=>$q->whereNull('share_expires_at')->orWhere('share_expires_at','>',now()))->firstOrFail(); abort_unless(Storage::disk($document->disk)->exists($document->path),404); return Storage::disk($document->disk)->download($document->path,$document->original_name); }

    public function destroy(Request $request, Document $document): RedirectResponse
    {
        $this->ownDocument($request,$document);
        $disk = Storage::disk($document->disk);
        if ($disk->exists($document->path)) {
            $disk->delete($document->path);
        }
        $document->delete();
        return back()->with('success','File deleted permanently.');
    }

    private function ownFolder(Request $request,DocumentFolder $folder): void { abort_unless($folder->user_id===$request->user()->id,403); }
    private function ownDocument(Request $request,Document $document): void { abort_unless($document->user_id===$request->user()->id,403); }
    private function isDescendant(DocumentFolder $candidate,DocumentFolder $ancestor): bool { while($candidate->parent_id){if((int)$candidate->parent_id===(int)$ancestor->id)return true;$candidate=$candidate->parent;} return false; }
    private function copyFolderTree(DocumentFolder $folder,int $userId,?int $parentId): void { $totalBytes=(int)$folder->documents()->sum('size'); foreach($folder->children()->get() as $child)$totalBytes+=(int)$child->documents()->sum('size'); $this->ensureQuotaAvailable($userId,$totalBytes); $copy=DocumentFolder::create(['user_id'=>$userId,'parent_id'=>$parentId,'name'=>$folder->name.' - Copy']); foreach($folder->documents()->get() as $document){$disk=Storage::disk($document->disk);$storedName=(string)Str::uuid().($document->extension?'.'.$document->extension:'');$copyPath="private/{$userId}/{$storedName}";if($disk->copy($document->path,$copyPath))Document::create(['user_id'=>$userId,'folder_id'=>$copy->id,'original_name'=>$document->original_name,'stored_name'=>$storedName,'disk'=>$document->disk,'path'=>$copyPath,'mime_type'=>$document->mime_type,'size'=>$document->size,'extension'=>$document->extension]);} foreach($folder->children()->get() as $child)$this->copyFolderTree($child,$userId,$copy->id); }
    private function deleteFolderTree(DocumentFolder $folder): void { foreach($folder->children()->get() as $child)$this->deleteFolderTree($child); foreach($folder->documents()->get() as $document){$disk=Storage::disk($document->disk);if($disk->exists($document->path))$disk->delete($document->path);$document->delete();}$folder->delete(); }
    private function usedBytes(int $userId): int { return (int)Document::where('user_id',$userId)->sum('size'); }
    private function ensureQuotaAvailable(int $userId,int $additionalBytes): void { $quota=(int)SystemSetting::query()->where('key','uploads.documents_quota_gb')->value('value'); $quotaBytes=($quota>0?$quota:50)*1073741824; $used=$this->usedBytes($userId); abort_if($used+$additionalBytes>$quotaBytes,422,'Storage quota exceeded.'); }
}
