<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paste_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('paste_id')->constrained()->cascadeOnDelete();
            $table->string('reason', 40);
            $table->text('details')->nullable();
            $table->string('reporter_ip_hash', 64);
            $table->timestamp('created_at')->useCurrent();
            $table->index(['paste_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paste_reports');
    }
};
