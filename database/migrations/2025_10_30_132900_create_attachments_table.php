<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('filename'); // Generated unique filename
            $table->string('original_name'); // Original filename from user
            $table->string('mime_type');
            $table->unsignedBigInteger('size'); // File size in bytes
            $table->string('path'); // Storage path
            $table->morphs('attachable'); // Polymorphic relation (task_id, comment_id, etc.)
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Indexes for performance
            $table->index(['attachable_type', 'attachable_id']);
            $table->index(['uploaded_by', 'created_at']);
            $table->index('mime_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachments');
    }
};
