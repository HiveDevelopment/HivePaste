<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\PasteData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HivePanelPasteController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        abort_unless(config('hivepaste.hivepanel_public_enabled'), 503, 'Public integration uploads are disabled.');

        // This endpoint is public. Do not trust claims that a request originated in HivePanel.
        $data = PasteData::validate($request);
        $content = $data['content'];
        $limit = min(524288, max(1, (int) config('hivepaste.hivepanel_max_bytes', 524288)));

        if (strlen($content) > $limit || str_contains($content, "\0") || ! mb_check_encoding($content, 'UTF-8')) {
            throw ValidationException::withMessages(['content' => 'Only UTF-8 text up to the integration size limit can be shared.']);
        }

        // Public integration pastes are always unlisted and expire after seven days.
        $paste = PasteData::create([
            'title' => $data['title'] ?? null,
            'content' => $content,
            'language' => $data['language'] ?? 'text',
            'expires_in' => '7d',
        ]);

        return response()->json(PasteData::response($paste), 201)
            ->header('Cache-Control', 'no-store');
    }
}
