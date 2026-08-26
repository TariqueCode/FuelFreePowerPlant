<?php

namespace App\Http\Controllers;

use App\Models\SiteContentItem;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ManagementController extends Controller
{
    public function __invoke(): View
    {
        $members = SiteContentItem::query()
            ->where('type', 'management')
            ->published()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('management.index', compact('members'));
    }

    public function vcard(SiteContentItem $member): Response
    {
        abort_unless($member->type === 'management' && $member->status === 'published', 404);

        $name = trim($member->title);
        $phone = trim((string) $member->phone);
        $email = trim((string) $member->email);
        $role = trim((string) ($member->designation ?: $member->excerpt));
        $escape = static fn (string $value): string => str_replace(["\\", ";", ",", "\n", "\r"], ["\\\\", "\\;", "\\,", "\\n", ""], $value);
        $parts = preg_split('/\s+/', $name) ?: [$name];
        $family = array_pop($parts) ?: '';
        $given = implode(' ', $parts);

        $lines = [
            'BEGIN:VCARD',
            'VERSION:3.0',
            'FN:' . $escape($name),
            'N:' . $escape($family) . ';' . $escape($given) . ';;;',
        ];
        if ($role !== '') $lines[] = 'TITLE:' . $escape($role);
        if ($phone !== '') $lines[] = 'TEL;TYPE=CELL:' . $escape($phone);
        if ($email !== '') $lines[] = 'EMAIL;TYPE=INTERNET:' . $escape($email);
        $lines[] = 'ORG:FuelFree PowerPlant';
        $lines[] = 'END:VCARD';

        return response(implode("\r\n", $lines) . "\r\n", 200, [
            'Content-Type' => 'text/vcard; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . str($name)->slug() . '.vcf"',
        ]);
    }
}
