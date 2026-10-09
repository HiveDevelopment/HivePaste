<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('pastes', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('slug', 20)->unique();
            $table->string('title', 120)->nullable();
            $table->longText('content');
            $table->string('language', 40)->default('text');
            $table->string('visibility', 20)->default('unlisted');
            $table->foreignId('api_token_id')->nullable()->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pastes'); }
};
