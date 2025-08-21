<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->morphs('reportable');
            $table->foreignId('anon_session_id')->constrained('anon_sessions');
            $table->enum('reason', ['spam', 'abuse', 'nsfw', 'other'])->default('other');
            $table->text('notes')->nullable();
            $table->enum('status', ['open', 'reviewed', 'dismissed'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
