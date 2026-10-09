<?php

namespace App\Support;

class SecretDetector
{
    public static function containsLikelySecret(string $content): bool
    {
        $patterns = [
            '/-----BEGIN (?:RSA |EC |OPENSSH |DSA )?PRIVATE KEY-----/i',
            '/\bgh[pousr]_[A-Za-z0-9_]{30,}\b/',
            '/\bgithub_pat_[A-Za-z0-9_]{30,}\b/',
            '/\bsk_(?:live|test)_[A-Za-z0-9]{16,}\b/',
            '/\bAKIA[0-9A-Z]{16}\b/',
            '/\b(?:password|passwd|secret_key|api_key|access_token)\s*[:=]\s*[\"\']?[^\s\"\']{12,}/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content) === 1) {
                return true;
            }
        }

        return false;
    }
}
