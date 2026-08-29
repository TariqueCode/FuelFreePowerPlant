@php
    $accent = '#51d8f0';
    $bg = '#020a10';
    $surface = '#071b25';
    $line = 'rgba(96,216,239,.20)';
    $text = '#edfaff';
    $muted = '#8eaab4';
    $displayChannel = $channel === 'career' ? 'CAREER TEAM' : 'CONTACT TEAM';
@endphp
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $subject ?: $companyName }}</title>
</head>
<body style="margin:0;padding:0;background:{{ $bg }};font-family:Arial,Helvetica,sans-serif;color:{{ $text }};">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:{{ $bg }};margin:0;padding:0;">
<tr><td align="center" style="padding:28px 14px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:680px;background:{{ $surface }};border:1px solid {{ $line }};border-radius:18px;overflow:hidden;">
<tr><td style="padding:24px 28px;background:#06151e;border-bottom:1px solid {{ $line }};">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td valign="middle">
@if($logoUrl)
<img src="{{ $logoUrl }}" alt="{{ $companyName }}" width="58" style="display:block;max-width:58px;height:auto;border:0;">
@endif
</td>
<td valign="middle" style="padding-left:14px;">
<div style="font-size:17px;line-height:1.3;font-weight:700;color:{{ $text }};letter-spacing:.2px;">{{ $companyName }}</div>
<div style="font-size:11px;line-height:1.5;color:{{ $accent }};margin-top:3px;">{{ $tagline }}</div>
</td>
</tr>
</table>
</td></tr>
<tr><td style="padding:30px 28px 26px;">
<div style="font-size:10px;line-height:1.4;font-weight:700;letter-spacing:1.8px;color:{{ $accent }};text-transform:uppercase;">{{ $displayChannel }}</div>
<h1 style="margin:9px 0 22px;font-size:25px;line-height:1.3;color:{{ $text }};">{{ $subject ?: 'Response from '.$companyName }}</h1>
<p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#c9dbe1;">Dear {{ $recipientName ?: 'Valued Recipient' }},</p>
<div style="font-size:14px;line-height:1.85;color:#c9dbe1;overflow-wrap:anywhere;">{!! nl2br(e($body)) !!}</div>
<div style="height:1px;background:rgba(96,216,239,.12);margin:28px 0;"></div>
<p style="margin:0;font-size:13px;line-height:1.7;color:{{ $muted }};">Thank you for contacting {{ $companyName }}. We appreciate your interest and look forward to assisting you.</p>
</td></tr>
<tr><td style="padding:20px 28px;background:#05131b;border-top:1px solid {{ $line }};">
<div style="font-size:12px;font-weight:700;color:{{ $text }};">{{ $companyName }}</div>
<div style="font-size:10px;line-height:1.7;color:{{ $muted }};margin-top:5px;">{{ $tagline }}</div>
@if($footerAddress)
<div style="font-size:10px;line-height:1.7;color:{{ $muted }};margin-top:10px;">{{ $footerAddress }}</div>
@endif
@if($footerPhone)
<div style="font-size:10px;line-height:1.7;color:{{ $muted }};">{{ $footerPhone }}</div>
@endif
<div style="margin-top:12px;"><a href="{{ $websiteUrl }}" style="font-size:10px;font-weight:700;color:{{ $accent }};text-decoration:none;">www.fuelfreepowerplant.com</a></div>
<div style="font-size:9px;line-height:1.6;color:#617d88;margin-top:14px;">This email was sent by {{ $companyName }} through the Help Desk.</div>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>