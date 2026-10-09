<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ListPasteReports extends Command
{
    protected $signature = 'hivepaste:reports {--limit=50 : Maximum reports to show}';
    protected $description = 'List abuse reports for host review';

    public function handle(): int
    {
        $limit = min(500, max(1, (int) $this->option('limit')));
        $rows = DB::table('paste_reports')->join('pastes', 'pastes.id', '=', 'paste_reports.paste_id')
            ->orderByDesc('paste_reports.id')->limit($limit)
            ->get(['paste_reports.id', 'pastes.slug', 'paste_reports.reason', 'paste_reports.created_at']);
        $this->table(['Report ID', 'Paste', 'Reason', 'Created'], $rows->map(fn ($row) => (array) $row)->all());
        return self::SUCCESS;
    }
}
