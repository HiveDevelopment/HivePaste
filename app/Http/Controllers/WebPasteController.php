<?php

namespace App\Http\Controllers;

use App\Models\Paste;
use App\Support\PasteData;
use App\Support\IpRedactor;
use App\Support\PasteManagement;
use App\Support\Turnstile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class WebPasteController extends Controller
{
    public function index(): View
    {
        return view('editor');
    }

    public function redactionPreview(Request $request): JsonResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string', function (string $attribute, mixed $value, \Closure $fail): void {
                if (strlen($value) > (int) config('hivepaste.max_paste_bytes')) {
                    $fail('The paste exceeds the maximum allowed size.');
                }
            }],
        ]);
        $redacted = IpRedactor::redact($data['content']);
        return response()->json([
            'content' => $redacted,
            'changed' => $redacted !== $data['content'],
        ])->header('Cache-Control', 'no-store, private');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(config('hivepaste.anonymous_enabled'), 403);
        Turnstile::verify($request);
        $data = PasteData::validate($request);
        if ($data['redact_ips'] ?? false) {
            $data['content'] = IpRedactor::redact($data['content']);
        }
        $paste = PasteData::create($data);
        $secret = PasteManagement::issue($paste);

        // Flash once: the management key must never appear in a public paste URL.
        return redirect()->route('pastes.show', $paste)
            ->with('management_key', $secret)
            ->with('status', 'Paste created. Save your private management link now; it cannot be recovered.');
    }

    public function show(Paste $paste): View
    {
        PasteData::visible($paste);

        return view('show', compact('paste'));
    }

    public function manage(Request $request, Paste $paste): View
    {
        PasteData::visible($paste);
        $secret = PasteManagement::authorize($request, $paste);

        return view('manage', compact('paste', 'secret'));
    }

    public function edit(Request $request, Paste $paste): View
    {
        PasteData::visible($paste);
        $secret = PasteManagement::authorize($request, $paste);

        return view('editor', compact('paste', 'secret'));
    }

    public function update(Request $request, Paste $paste): RedirectResponse
    {
        PasteData::visible($paste);
        $secret = PasteManagement::authorize($request, $paste);
        $data = PasteData::validate($request);
        if ($data['redact_ips'] ?? false) {
            $data['content'] = IpRedactor::redact($data['content']);
        }
        $paste->update([
            'title' => $data['title'] ?? null,
            'content' => $data['content'],
            'language' => $data['language'] ?? 'text',
            'visibility' => 'unlisted',
            'expires_at' => PasteData::expiration($data['expires_in'] ?? config('hivepaste.default_expiration')),
        ]);

        return redirect()->route('pastes.manage', ['paste' => $paste, 'key' => $secret])
            ->with('status', 'Paste updated.');
    }

    public function destroy(Request $request, Paste $paste): RedirectResponse
    {
        PasteData::visible($paste);
        PasteManagement::authorize($request, $paste);
        $paste->delete();

        return redirect()->route('home')->with('status', 'Paste deleted.');
    }

    public function download(Paste $paste): Response
    {
        PasteData::visible($paste);

        $extension = match ($paste->language) {
            'json' => 'json', 'javascript' => 'js', 'typescript' => 'ts',
            'php' => 'php', 'python' => 'py', 'java' => 'java',
            'yaml' => 'yaml', 'bash' => 'sh', 'sql' => 'sql',
            'html' => 'html', 'css' => 'css', 'go' => 'go',
            'rust' => 'rs', 'xml' => 'xml', 'log' => 'log',
            default => 'txt',
        };

        return response($paste->content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'no-store, private',
            'Content-Disposition' => 'attachment; filename="'.$paste->slug.'.'.$extension.'"',
        ]);
    }

    public function raw(Paste $paste): Response
    {
        PasteData::visible($paste);

        return response($paste->content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'no-store, private',
            'Content-Disposition' => 'inline',
        ]);
    }
}
