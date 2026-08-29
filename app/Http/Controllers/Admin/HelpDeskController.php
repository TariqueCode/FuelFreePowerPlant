<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerApplication;
use App\Models\EmailAccount;
use App\Models\HelpdeskEmail;
use App\Models\HelpdeskReply;
use App\Models\Inquiry;
use App\Models\SystemSetting;
use App\Services\WebmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class HelpDeskController extends Controller
{
    public function index(Request $request): View
    {
        $items = collect();

        Inquiry::query()->latest()->get()->each(function ($item) use ($items) {
            $items->push((object) [
                'type'=>'contact','channel'=>'contact','id'=>$item->id,'name'=>$item->name,'email'=>$item->email,
                'subject'=>$item->subject ?: '(No subject)','message'=>$item->message,'status'=>$item->status,
                'received_at'=>$item->created_at,'route'=>route('admin.helpdesk.show',['type'=>'contact','id'=>$item->id]),
            ]);
        });

        CareerApplication::query()->latest()->get()->each(function ($item) use ($items) {
            $items->push((object) [
                'type'=>'career','channel'=>'career','id'=>$item->id,'name'=>$item->name,'email'=>$item->email,
                'subject'=>'Career application'.($item->position ? ': '.$item->position : ''),
                'message'=>$item->message ?: '','status'=>$item->status,'received_at'=>$item->created_at,
                'route'=>route('admin.helpdesk.show',['type'=>'career','id'=>$item->id]),
            ]);
        });

        HelpdeskEmail::query()->latest('received_at')->get()->each(function ($item) use ($items) {
            $items->push((object) [
                'type'=>'email','channel'=>$item->mailbox_group,'id'=>$item->id,
                'name'=>$item->sender_name ?: $item->sender_email,'email'=>$item->sender_email,
                'subject'=>$item->subject ?: '(No subject)','message'=>$item->body_text,'status'=>$item->status,
                'received_at'=>$item->received_at ?: $item->created_at,
                'route'=>route('admin.helpdesk.show',['type'=>'email','id'=>$item->id]),
            ]);
        });

        $allItems = $items->sortByDesc('received_at')->values();
        $search = trim((string) $request->query('q', ''));
        $channel = (string) $request->query('channel', 'all');
        $status = (string) $request->query('status', 'all');

        $items = $allItems->filter(function ($item) use ($search, $channel, $status) {
            if ($channel !== 'all' && $item->channel !== $channel) return false;
            if ($status !== 'all' && $item->status !== $status) return false;
            if ($search === '') return true;

            $haystack = mb_strtolower(implode(' ', [
                $item->name, $item->email, $item->subject, $item->message, $item->status, $item->channel,
            ]));
            return str_contains($haystack, mb_strtolower($search));
        })->values();

        $page=max(1,(int)$request->query('page',1));
        $perPage=20;
        $pageItems=$items->slice(($page-1)*$perPage,$perPage)->values();
        $paginator=new \Illuminate\Pagination\LengthAwarePaginator(
            $pageItems,$items->count(),$perPage,$page,['path'=>$request->url(),'query'=>$request->query()]
        );

        $contactCount=$allItems->where('channel','contact')->count();
        $careerCount=$allItems->where('channel','career')->count();
        $openCount=$allItems->filter(fn($item) => in_array($item->status,['new','read','in_progress','reviewing'],true))->count();
        $repliedCount=$allItems->where('status','replied')->count();
        $unreadCount=$allItems->where('status','new')->count();

        return view('admin.helpdesk.index',[
            'items'=>$paginator,'openCount'=>$openCount,'contactCount'=>$contactCount,'careerCount'=>$careerCount,
            'repliedCount'=>$repliedCount,'unreadCount'=>$unreadCount,'search'=>$search,'channel'=>$channel,'status'=>$status,
        ]);
    }

    public function show(string $type,int $id): View
    {
        [$source,$label]=$this->source($type,$id);
        $replies=HelpdeskReply::query()->where('source_type',$type)->where('source_id',$id)->with('adminUser')->latest()->get();

        if($type==='contact' && !$source->read_at){
            $source->update(['read_at'=>now(),'status'=>$source->status==='new'?'read':$source->status]);
        } elseif($type==='email' && $source->status==='new'){
            $source->update(['status'=>'read']);
        }

        return view('admin.helpdesk.show',compact('source','label','type','replies'));
    }

    public function updateStatus(Request $request, string $type, int $id): RedirectResponse
    {
        [$source] = $this->source($type, $id);

        $allowed = match ($type) {
            'career' => ['new','reviewing','shortlisted','rejected','hired'],
            default => ['new','read','in_progress','replied','closed'],
        };

        $data = $request->validate(['status' => ['required','in:'.implode(',', $allowed)]]);
        $source->update(['status' => $data['status']]);

        return back()->with('status', 'Status updated successfully.');
    }

    public function reply(Request $request,string $type,int $id,WebmailService $webmail): RedirectResponse
    {
        [$source]=$this->source($type,$id);
        $data=$request->validate(['body'=>['required','string','max:500000']]);

        if($type==='email'){
            $to=trim((string)$source->sender_email);
            $subject=str_starts_with(strtolower((string)$source->subject),'re:')
                ? $source->subject : 'Re: '.($source->subject ?: '(No subject)');
            $mailboxId=(int)$source->email_account_id;
        } else {
            $to=trim((string)$source->email);
            $subject=$type==='contact'
                ? (str_starts_with(strtolower($source->subject),'re:')?$source->subject:'Re: '.$source->subject)
                : 'Re: Career application'.($source->position?': '.$source->position:'');
            $settings=SystemSetting::query()->pluck('value','key')->all();
            $mailboxId=(int)($settings[$type==='career'?'mail.career_account_id':'mail.contact_account_id']??0);
        }

        $address=($type==='career'||($type==='email'&&$source->mailbox_group==='career'))
            ?'career@fuelfreepowerplant.com':'info@fuelfreepowerplant.com';
        $account=$mailboxId
            ?EmailAccount::query()->whereKey($mailboxId)->where('status','active')->first()
            :EmailAccount::query()->where('address',$address)->where('status','active')->first();

        if(!$account) return back()->withErrors(['reply'=>'No active official mailbox is configured for this Help Desk channel.']);

        $reply=HelpdeskReply::create([
            'source_type'=>$type,'source_id'=>$id,'admin_user_id'=>$request->user()->id,'to_email'=>$to,
            'subject'=>$subject,'body'=>$data['body'],'status'=>'pending',
        ]);

        try{
            $webmail->send(
                $account->address,$account->password,$to,$subject,$data['body'],
                ['imap_host'=>$account->imap_host,'imap_port'=>$account->imap_port,'smtp_host'=>$account->smtp_host,'smtp_port'=>$account->smtp_port],
                null,false
            );
            $reply->update(['status'=>'sent','sent_at'=>now(),'error'=>null]);
            if($type==='email'){
                $source->update(['status'=>'replied']);
            } elseif($type==='contact'){
                $source->update(['status'=>'in_progress']);
            } else {
                $source->update(['status'=>'reviewing']);
            }
            return back()->with('status','Reply sent successfully from '.$account->address.'.');
        }catch(Throwable $e){
            report($e);
            $reply->update(['status'=>'failed','error'=>mb_substr($e->getMessage(),0,5000)]);
            return back()->withErrors(['reply'=>'The reply could not be sent. Check the official mailbox connection and try again.']);
        }
    }

    public function deleteEmail(Request $request,int $id): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('mail.manage'),403);
        $email=HelpdeskEmail::query()->with('attachments')->findOrFail($id);
        foreach($email->attachments as $attachment){
            if($attachment->path) Storage::disk('local')->delete($attachment->path);
        }
        $email->delete();
        return redirect()->route('admin.helpdesk')->with('status','Help Desk email permanently deleted from the application server.');
    }

    public function downloadAttachment(Request $request,int $id): mixed
    {
        abort_unless($request->user()->hasPermission('mail.view'),403);
        $attachment=\App\Models\HelpdeskEmailAttachment::query()->findOrFail($id);
        abort_unless(Storage::disk('local')->exists($attachment->path),404);
        return Storage::disk('local')->download($attachment->path,$attachment->filename,[
            'Content-Type'=>$attachment->mime_type ?: 'application/octet-stream',
        ]);
    }

    private function source(string $type,int $id): array
    {
        if($type==='contact') return [Inquiry::query()->findOrFail($id),'Contact inquiry'];
        if($type==='career') return [CareerApplication::query()->findOrFail($id),'Career application'];
        if($type==='email'){
            $source=HelpdeskEmail::query()->with('attachments')->findOrFail($id);
            return [$source,$source->mailbox_group==='career'?'Career mailbox':'Contact mailbox'];
        }
        abort(404);
    }
}
