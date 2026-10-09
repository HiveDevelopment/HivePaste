<?php

namespace App\Support;

use App\Models\Paste;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PasteManagement
{
    public static function issue(Paste $paste): string
    {
        $secret = Str::random(64);
        $paste->forceFill(['management_token_hash' => hash('sha256', $secret)])->save();

        return $secret;
    }

    public static function allowed(Paste $paste, ?string $secret): bool
    {
        return $paste->api_token_id === null
            && $paste->management_token_hash !== null
            && is_string($secret)
            && strlen($secret) === 64
            && hash_equals($paste->management_token_hash, hash('sha256', $secret));
    }

    public static function authorize(Request $request, Paste $paste): string
    {
        $secret = $request->input('management_key') ?? $request->query('key');
        abort_unless(self::allowed($paste, $secret), 403);

        return $secret;
    }
}
