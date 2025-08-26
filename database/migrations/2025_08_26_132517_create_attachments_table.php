<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_attachments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable');        // attachable_id, attachable_type
            $table->string('path');              // storage path (public disk)
            $table->string('mime')->nullable();
            $table->unsignedInteger('size')->nullable();   // bytes
            $table->unsignedInteger('width')->nullable();  // px
            $table->unsignedInteger('height')->nullable(); // px
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
