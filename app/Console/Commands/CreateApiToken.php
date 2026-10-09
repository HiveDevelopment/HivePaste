<?php
namespace App\Console\Commands;
use App\Models\ApiToken;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
class CreateApiToken extends Command
{
    protected $signature = 'hivepaste:token:create {--name= : A descriptive integration name}';
    protected $description = 'Create an API bearer token (shown once)';
    public function handle(): int
    {
        $token = 'hp_'.Str::random(64);
        ApiToken::create(['name'=>$this->option('name') ?: 'integration', 'token_hash'=>hash('sha256', $token)]);
        $this->warn('Store this token securely. It will not be shown again.');
        $this->line($token);
        return self::SUCCESS;
    }
}
