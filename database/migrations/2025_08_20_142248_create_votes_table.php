<?php

// database/migrations/2025_09_30_000000_create_votes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();

            // Polymorphic: thread/post/comment/reply
            $table->morphs('votable'); // votable_type, votable_id (unsigned big int)

            // Identitas pemilih
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('anon_key')->nullable(); // simpan fingerprint anon dari session/cookie

            // Nilai vote: -1 atau +1
            $table->tinyInteger('value'); // -1 || +1
            $table->timestamps();

            // 1 vote per user per konten
            $table->unique(['votable_type', 'votable_id', 'user_id']);

            // 1 vote per anon_key per konten
            $table->unique(['votable_type', 'votable_id', 'anon_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
