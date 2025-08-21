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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->morphs('votable'); // votable_type + votable_id
            $table->foreignId('anon_session_id')->constrained('anon_sessions');
            $table->tinyInteger('value'); // -1 atau 1
            $table->timestamps();

            $table->unique(['votable_type', 'votable_id', 'anon_session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
