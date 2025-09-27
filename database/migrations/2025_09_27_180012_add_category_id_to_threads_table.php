<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->nullable()
                ->after('board_id')
                ->constrained('categories')
                ->nullOnDelete(); // jika kategori dihapus, set null
            $table->index(['board_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropIndex(['board_id', 'category_id']);
            $table->dropColumn('category_id');
        });
    }
};
