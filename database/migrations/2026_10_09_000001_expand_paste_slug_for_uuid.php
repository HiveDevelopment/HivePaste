<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pastes', function (Blueprint $table): void {
            $table->string('slug', 36)->change();
        });
    }

    public function down(): void
    {
        // Keep the larger column: shortening it could truncate existing UUID links.
    }
};
