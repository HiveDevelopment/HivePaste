<?php

namespace App\Support;

class IpRedactor
{
    public static function redact(string $content): string
    {
        // Match possible IP tokens, then validate to avoid redacting versions and timestamps.
        $content = preg_replace_callback('/(?<![\w.])(?:\d{1,3}\.){3}\d{1,3}(?![\w.])/', static function (array $match): string {
            return filter_var($match[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? '[REDACTED IP]' : $match[0];
        }, $content);

        // IPv6 candidates can contain a scope suffix; preserve punctuation and port suffixes.
        return preg_replace_callback('/(?<![\w:])(?:[0-9a-fA-F:.]*:[0-9a-fA-F:.]+)(?:%[a-zA-Z0-9_.-]+)?(?![\w:])/', static function (array $match): string {
            $candidate = explode('%', $match[0], 2)[0];
            return filter_var($candidate, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? '[REDACTED IP]' : $match[0];
        }, $content);
    }
}
