@extends('layouts.portal')
@section('title', $folder->name.' · Profile Builder')
@section('content')
<section class="hero">
    <div>
        <a class="back" href="{{ route('admin.profile-builder.index') }}"><i class="fa-solid fa-arrow-left"></i> Profile Builder</a>
        <span class="eyebrow">PROFILE FOLDER</span>
        <h1>{{ $folder->name }}</h1>
        <p>{{ $folder->profiles_count }} {{ $folder->profiles_count === 1 ? 'profile' : 'profiles' }} in this folder.</p>
    </div>
    <div class="hero-actions">
        <a class="primary" href="{{ route('admin.profile-builder.create',['management_profile_folder_id'=>$folder->id]) }}"><i class="fa-solid fa-user-plus"></i> Add profile</a>
    </div>
</section>
@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
@if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif
<article class="panel">
    <div class="panel-head">
        <div><span class="eyebrow">FOLDER CONTENT</span><h2>Profiles</h2></div>
        <span class="status {{ $folder->status }}">{{ ucfirst($folder->status) }}</span>
    </div>
    <div class="profiles">
        @forelse($folder->profiles as $member)
            <div class="profile-row">
                <div class="avatar">@if($member->image_path)<img src="{{ asset('storage/'.$member->image_path) }}" alt="">@else<i class="fa-solid fa-user-tie"></i>@endif</div>
                <div class="profile-info"><strong>{{ $member->title }}</strong><span>{{ $member->designation }}</span><small>{{ $member->email ?: 'No official email' }} · {{ $member->phone }}</small></div>
                <span class="profile-status {{ $member->status }}">{{ ucfirst($member->status) }}</span>
                <div class="row-actions">
                    <a href="{{ route('admin.profile-builder.edit',$member) }}" title="Edit profile"><i class="fa-solid fa-pen"></i></a>
                    <form method="POST" action="{{ route('admin.profile-builder.toggle',$member) }}">@csrf @method('PATCH')<button type="submit" title="{{ $member->status==='published'?'Deactivate':'Activate' }}"><i class="fa-solid {{ $member->status==='published'?'fa-toggle-on':'fa-toggle-off' }}"></i></button></form>
                    <form method="POST" action="{{ route('admin.profile-builder.destroy',$member) }}" onsubmit="return confirm('Delete this profile?')">@csrf @method('DELETE')<button type="submit" title="Delete"><i class="fa-solid fa-trash"></i></button></form>
                </div>
            </div>
        @empty
            <div class="empty"><i class="fa-solid fa-users-slash"></i><h3>No profiles yet</h3><p>Add the first leadership profile to this folder.</p><a class="primary" href="{{ route('admin.profile-builder.create',['management_profile_folder_id'=>$folder->id]) }}"><i class="fa-solid fa-user-plus"></i> Add profile</a></div>
        @endforelse
    </div>
</article>
@endsection
@push('styles')<style>
.hero{display:flex;align-items:flex-end;justify-content:space-between;gap:20px;margin-bottom:18px}.back{display:inline-flex;gap:7px;align-items:center;color:#6bcfe5;text-decoration:none;font-size:10px;margin-bottom:10px}.eyebrow{display:block;font-size:9px;letter-spacing:.16em;color:#54cde8}.hero h1{margin:6px 0;font-size:clamp(27px,4vw,42px)}.hero p{margin:0;color:#7898a5;font-size:11px}.hero-actions{display:flex;gap:8px}.primary{display:inline-flex;align-items:center;justify-content:center;gap:8px;border-radius:11px;padding:11px 14px;text-decoration:none;font-size:10px;font-weight:800;background:linear-gradient(135deg,#25abc9,#1687a4);color:#fff}.notice,.errors{padding:11px 13px;margin-bottom:12px;border-radius:11px;font-size:10px}.notice{background:rgba(49,191,139,.1);color:#a8e5ca}.errors{background:rgba(210,65,65,.12);color:#ffb0b0}.panel{border:1px solid var(--line);border-radius:18px;background:linear-gradient(145deg,rgba(8,35,47,.96),rgba(3,20,29,.98));overflow:hidden}.panel-head{display:flex;align-items:center;justify-content:space-between;padding:16px;border-bottom:1px solid rgba(91,214,239,.08)}.panel-head h2{margin:5px 0 0;color:#e8f8fb;font-size:18px}.status,.profile-status{font-size:8px;border-radius:999px;padding:6px 8px}.published{color:#9ee7ca;background:rgba(49,191,139,.09);border:1px solid rgba(49,191,139,.15)}.draft{color:#f2c78d;background:rgba(220,153,68,.09);border:1px solid rgba(220,153,68,.15)}.profiles{padding:0 14px}.profile-row{display:grid;grid-template-columns:48px minmax(0,1fr) auto auto;align-items:center;gap:12px;padding:12px 4px;border-bottom:1px solid rgba(91,214,239,.07)}.profile-row:last-child{border-bottom:0}.avatar{width:48px;height:48px;border-radius:12px;overflow:hidden;background:#092633;display:grid;place-items:center;color:#4ec7e2;font-size:18px}.avatar img{width:100%;height:100%;object-fit:cover}.profile-info{min-width:0}.profile-info strong,.profile-info span,.profile-info small{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.profile-info strong{color:#e4f6f8;font-size:12px}.profile-info span{margin-top:3px;color:#62c9df;font-size:10px}.profile-info small{margin-top:4px;color:#7898a5;font-size:9px}.row-actions{display:flex;align-items:center;gap:5px}.row-actions a,.row-actions button{width:31px;height:31px;border:1px solid transparent;border-radius:8px;background:transparent;color:#75949e;display:grid;place-items:center;text-decoration:none;cursor:pointer}.row-actions a:hover{color:#64d8ef;background:rgba(67,209,240,.07)}.row-actions button:hover{color:#ff9da4;background:rgba(231,83,91,.08)}.empty{text-align:center;padding:55px 20px;color:#7898a5}.empty i{font-size:32px;color:#55cce7}.empty h3{color:#e1f5f8;margin:12px 0 5px;font-size:17px}.empty p{font-size:10px;margin:0 auto 16px}.empty .primary{display:inline-flex}@media(max-width:650px){.hero{align-items:flex-start;flex-direction:column}.hero-actions,.hero-actions>*{width:100%}.profile-row{grid-template-columns:42px minmax(0,1fr) auto}.avatar{width:42px;height:42px}.profile-status{grid-column:2}.row-actions{grid-column:2/-1;justify-content:flex-start}}
</style>@endpush
