<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_edited_at_to_comments_and_threads.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->timestamp('edited_at')->nullable()->after('updated_at');
        });

        // optional: kalau mau untuk thread juga
        Schema::table('threads', function (Blueprint $table) {
            $table->timestamp('edited_at')->nullable()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('edited_at');
        });

        Schema::table('threads', function (Blueprint $table) {
            $table->dropColumn('edited_at');
        });
    }
};
