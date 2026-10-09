<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class Turnstile
{
    public static function verify(Request $request): void
    {
        $secret = config('hivepaste.turnstile_secret_key');
        $site = config('hivepaste.turnstile_site_key');
        if (! $secret && ! $site) {
            return;
        }
        if (! $secret || ! $site) {
            throw ValidationException::withMessages(['captcha' => 'CAPTCHA is not configured correctly on this host.']);
        }
        $token = $request->input('cf-turnstile-response');
        if (! is_string($token) || $token === '' || strlen($token) > 2048) {
            throw ValidationException::withMessages(['captcha' => 'Complete the CAPTCHA to continue.']);
        }
        try {
            $result = Http::asForm()->timeout(5)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $request->ip(),
            ]);
            if ($result->successful() && $result->json('success') === true) {
                return;
            }
        } catch (\Throwable $exception) {
            report($exception);
        }
        throw ValidationException::withMessages(['captcha' => 'CAPTCHA verification failed. Please try again.']);
    }
}
