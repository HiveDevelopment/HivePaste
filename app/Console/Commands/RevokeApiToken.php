<?php

namespace App\Console\Commands;

use App\Models\ApiToken;
use Illuminate\Console\Command;

class RevokeApiToken extends Command
{
    protected $signature = 'hivepaste:token:revoke {id : Numeric token ID from hivepaste:token:list}';
    protected $description = 'Revoke a host-issued API token';

    public function handle(): int
    {
        $token = ApiToken::find($this->argument('id'));
        if (! $token) {
            $this->error('Token not found.');
            return self::FAILURE;
        }
        $token->update(['revoked_at' => now()]);
        $this->info('Token revoked.');
        return self::SUCCESS;
    }
}
