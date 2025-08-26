<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // drop old FK first (adjust name if different)
            $table->dropForeign(['anon_session_id']);

            // make nullable
            $table->unsignedBigInteger('anon_session_id')->nullable()->change();

            // re-add FK, set null on delete (nice to have)
            $table->foreign('anon_session_id')
                ->references('id')->on('anon_sessions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['anon_session_id']);
            $table->unsignedBigInteger('anon_session_id')->nullable(false)->change();
            $table->foreign('anon_session_id')
                ->references('id')->on('anon_sessions');
        });
    }
};
