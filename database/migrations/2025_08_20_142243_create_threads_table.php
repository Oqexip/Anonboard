<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('anon_session_id')->nullable()->constrained('anon_sessions')->nullOnDelete();
            $table->string('title')->nullable();
            $table->longText('content');
            $table->integer('score')->default(0);
            $table->integer('comment_count')->default(0);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['board_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threads');
    }
};
