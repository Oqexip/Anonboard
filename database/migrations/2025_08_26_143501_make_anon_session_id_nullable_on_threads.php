<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            // Hapus foreign key lama dulu (nama FK kamu bisa beda; gunakan yang di error log).
            $table->dropForeign(['anon_session_id']);

            // Jadikan nullable
            $table->unsignedBigInteger('anon_session_id')->nullable()->change();

            // Tambahkan FK lagi, dengan on delete set null (opsional tapi ideal)
            $table->foreign('anon_session_id')
                ->references('id')->on('anon_sessions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->dropForeign(['anon_session_id']);
            $table->unsignedBigInteger('anon_session_id')->nullable(false)->change();
            $table->foreign('anon_session_id')
                ->references('id')->on('anon_sessions');
        });
    }
};
