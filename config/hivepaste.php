<?php

return [
    'legal_updated' => env('HIVEPASTE_LEGAL_UPDATED', 'Not yet reviewed'),
    'anonymous_enabled' => (bool) env('HIVEPASTE_ANONYMOUS_ENABLED', true),
    'api_enabled' => (bool) env('HIVEPASTE_API_ENABLED', true),
    'max_paste_bytes' => (int) env('HIVEPASTE_MAX_PASTE_BYTES', 524288),
    'web_rate_limit' => (int) env('HIVEPASTE_WEB_RATE_LIMIT', 10),
    'api_rate_limit' => (int) env('HIVEPASTE_API_RATE_LIMIT', 30),
    'default_expiration' => env('HIVEPASTE_DEFAULT_EXPIRATION', '7d'),
    'secret_detection' => (bool) env('HIVEPASTE_SECRET_DETECTION', true),
    'turnstile_site_key' => env('HIVEPASTE_TURNSTILE_SITE_KEY', ''),
    'turnstile_secret_key' => env('HIVEPASTE_TURNSTILE_SECRET_KEY', ''),
    'report_rate_limit' => (int) env('HIVEPASTE_REPORT_RATE_LIMIT', 3),
    'allow_never_expire' => (bool) env('HIVEPASTE_ALLOW_NEVER_EXPIRE', false),
];
