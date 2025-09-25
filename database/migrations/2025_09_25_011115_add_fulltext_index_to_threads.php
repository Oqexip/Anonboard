<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (config('database.default') === 'mysql') {
            Schema::table('threads', function (Blueprint $table) {
                // Tambah FULLTEXT index untuk title + content
                $table->fullText(['title', 'content'], 'threads_title_content_fulltext');
            });
        }
    }

    public function down(): void
    {
        if (config('database.default') === 'mysql') {
            Schema::table('threads', function (Blueprint $table) {
                $table->dropFullText('threads_title_content_fulltext');
            });
        }
    }
};
