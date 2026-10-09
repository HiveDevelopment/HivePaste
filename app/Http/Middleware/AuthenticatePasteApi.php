<?php
namespace App\Http\Middleware;
use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class AuthenticatePasteApi
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('hivepaste.api_enabled')) {
            return response()->json(['message' => 'API disabled.'], 403);
        }
        $token = $request->bearerToken();
        if (! $token || ! str_starts_with($token, 'hp_')) {
            return response()->json(['message'=>'Unauthenticated.'], 401);
        }
        $record = ApiToken::query()->where('token_hash', hash('sha256', $token))->whereNull('revoked_at')->first();
        if (! $record) { return response()->json(['message'=>'Unauthenticated.'], 401); }
        $request->attributes->set('paste_api_token', $record);
        $record->forceFill(['last_used_at'=>now()])->save();
        return $next($request);
    }
}
