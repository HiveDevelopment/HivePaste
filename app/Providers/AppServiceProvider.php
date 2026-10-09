<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('paste-web', fn (Request $request) => Limit::perMinute(
            max(1, (int) config('hivepaste.web_rate_limit'))
        )->by($request->ip()));

        RateLimiter::for('paste-report', fn (Request $request) => Limit::perHour(
            max(1, (int) config('hivepaste.report_rate_limit'))
        )->by($request->ip()));

        RateLimiter::for('paste-hivepanel-public', function (Request $request): array {
            $ip = $request->ip() ?? 'unknown';

            return [
                Limit::perMinute(max(1, (int) config('hivepaste.hivepanel_rate_limit', 5)))->by('hivepanel:ip:'.$ip),
                Limit::perDay(max(1, (int) config('hivepaste.hivepanel_daily_ip_limit', 50)))->by('hivepanel:day:'.$ip),
                Limit::perHour(max(1, (int) config('hivepaste.hivepanel_global_hourly_limit', 300)))->by('hivepanel:global:hour'),
                Limit::perDay(max(1, (int) config('hivepaste.hivepanel_global_daily_limit', 2000)))->by('hivepanel:global:day'),
            ];
        });

        RateLimiter::for('paste-api', fn (Request $request) => Limit::perMinute(
            max(1, (int) config('hivepaste.api_rate_limit'))
        )->by($request->attributes->get('paste_api_token')?->id ?? $request->ip()));
    }
}
