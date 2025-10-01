<?php

// database/migrations/2025_09_30_000010_add_score_columns_if_missing.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        foreach (['threads','posts','comments','replies'] as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table,'score')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->integer('score')->default(0)->index();
                });
            }
        }
    }

    public function down(): void {
        foreach (['threads','posts','comments','replies'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table,'score')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('score');
                });
            }
        }
    }
};
