<?php

namespace App\Http\Controllers;

use App\Models\ManagementProfileFolder;
use App\Models\SiteContentItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ManagementController extends Controller
{
    public function __invoke(): Response|\Illuminate\Http\RedirectResponse
    {
        $folder=ManagementProfileFolder::query()->where('status','published')->orderBy('sort_order')->orderBy('id')->first();
        if(!$folder) abort(404);
        return Redirect::to('/'.$folder->slug);
    }

    public function folderFallback(Request $request): View
    {
        $slug=trim($request->path(),'/');
        if($slug==='' || str_contains($slug,'/')) abort(404);
        return $this->folder($slug);
    }

    public function folder(string $folderSlug): View
    {
        $folder=ManagementProfileFolder::query()->where('slug',$folderSlug)->where('status','published')->firstOrFail();
        $members=$folder->profiles()->published()->orderBy('sort_order')->orderBy('title')->get();
        return view('management.folder',compact('folder','members'));
    }

    public function vcard(SiteContentItem $member): Response
    {
        abort_unless($member->type==='management'&&$member->status==='published',404);
        $name=trim($member->title);$phone=trim((string)$member->phone);$email=trim((string)$member->email);$role=trim((string)($member->designation?:$member->excerpt));
        $escape=static fn(string $value):string=>str_replace(["\\",";",",","\n","\r"],["\\\\","\\;","\\,","\\n",""],$value);$parts=preg_split('/\s+/',$name)?:[$name];$family=array_pop($parts)?:'';$given=implode(' ',$parts);
        $lines=['BEGIN:VCARD','VERSION:3.0','FN:'.$escape($name),'N:'.$escape($family).';'.$escape($given).';;;'];if($role!=='')$lines[]='TITLE:'.$escape($role);if($phone!=='')$lines[]='TEL;TYPE=CELL:'.$escape($phone);if($email!=='')$lines[]='EMAIL;TYPE=INTERNET:'.$escape($email);$lines[]='ORG:FuelFree PowerPlant';$lines[]='END:VCARD';
        return response(implode("\r\n",$lines)."\r\n",200,['Content-Type'=>'text/vcard; charset=utf-8','Content-Disposition'=>'attachment; filename="'.str($name)->slug().'.vcf"']);
    }
}
