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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // nama admin/moderator
            $table->string('email')->unique(); // email login
            $table->string('password'); // password bcrypt
            $table->enum('role', ['admin', 'moderator'])->default('moderator'); // role
            $table->rememberToken(); // untuk fitur remember me
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
