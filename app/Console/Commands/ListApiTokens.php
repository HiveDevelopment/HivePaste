<?php

namespace App\Console\Commands;

use App\Models\ApiToken;
use Illuminate\Console\Command;

class ListApiTokens extends Command
{
    protected $signature = 'hivepaste:token:list';
    protected $description = 'List API token names and status without exposing secrets';

    public function handle(): int
    {
        $this->table(['ID', 'Name', 'Last used', 'Revoked'], ApiToken::query()->orderBy('id')->get()->map(
            fn (ApiToken $token) => [$token->id, $token->name, $token->last_used_at?->toDateTimeString() ?? 'Never', $token->revoked_at ? 'Yes' : 'No']
        )->all());

        return self::SUCCESS;
    }
}
