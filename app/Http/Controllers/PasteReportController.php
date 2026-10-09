<?php

namespace App\Http\Controllers;

use App\Models\Paste;
use App\Support\PasteData;
use App\Support\Turnstile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PasteReportController extends Controller
{
    public function store(Request $request, Paste $paste): RedirectResponse
    {
        PasteData::visible($paste);
        Turnstile::verify($request);
        $data = $request->validate([
            'reason' => ['required', Rule::in(['spam', 'malware', 'personal_information', 'illegal_content', 'other'])],
            'details' => ['nullable', 'string', 'max:1000'],
            'website' => ['nullable', 'max:0'], // Honeypot: legitimate visitors leave blank.
        ]);
        DB::table('paste_reports')->insert([
            'paste_id' => $paste->id,
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
            'reporter_ip_hash' => hash_hmac('sha256', (string) $request->ip(), config('app.key')),
            'created_at' => now(),
        ]);
        return back()->with('status', 'Report received. Thank you.');
    }
}
