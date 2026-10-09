<?php
namespace App\Support;
use App\Models\Paste;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Support\SecretDetector;
class PasteData
{
    public static function validate(Request $request): array
    {
        return $request->validate([
            'title'=>['nullable','string','max:120'],
            'redact_ips'=>['sometimes','boolean'],
            'content'=>['required','string', function ($attribute, $value, $fail): void {
                if (config('hivepaste.secret_detection') && SecretDetector::containsLikelySecret($value)) {
                    $fail('Possible credentials detected. Remove secrets before sharing this paste.');
                }
                if (strlen($value) > config('hivepaste.max_paste_bytes')) {
                    $fail('The paste exceeds the maximum allowed size.');
                }
            }],
            'language'=>['nullable','string','max:40','regex:/^[a-zA-Z0-9_+.-]+$/'],
            'visibility'=>['nullable','in:unlisted'],
            'expires_in'=>['nullable', Rule::in(config('hivepaste.allow_never_expire') ? ['1h', '1d', '7d', '30d', 'never'] : ['1h', '1d', '7d', '30d'])],
        ]);
    }
    public static function create(array $data, ?int $tokenId = null): Paste
    {
        $expiration = self::expiration($data['expires_in'] ?? config('hivepaste.default_expiration'));
        return Paste::create([
            'slug'=>Paste::newSlug(), 'title'=>$data['title'] ?? null,
            'content'=>$data['content'], 'language'=>$data['language'] ?? 'text',
            'visibility'=>'unlisted',
            'expires_at'=>$expiration, 'api_token_id'=>$tokenId,
        ]);
    }
    public static function expiration(string $duration): ?\Illuminate\Support\Carbon
    {
        return match ($duration) {
            '1h'=>now()->addHour(), '1d'=>now()->addDay(),
            '30d'=>now()->addDays(30), 'never'=>null,
            default=>now()->addDays(7),
        };
    }
    public static function visible(Paste $paste): void
    {
        abort_if($paste->isExpired(), 404);
    }
    public static function response(Paste $paste): array
    {
        return [
            'id'=>$paste->slug,
            'url'=>route('pastes.show', $paste),
            'raw_url'=>route('pastes.raw', $paste),
            'title'=>$paste->title,
            'language'=>$paste->language,
            'visibility'=>$paste->visibility,
            'expires_at'=>$paste->expires_at?->toIso8601String(),
        ];
    }
}
