<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // created, updated, deleted, status_changed, assigned, etc.
            $table->text('description');
            $table->json('properties')->nullable(); // Store old/new values
            $table->morphs('subject'); // The model that was acted upon (Task, Epic, Project)
            $table->foreignId('causer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['project_id', 'created_at']);
            $table->index(['causer_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities');
    }
}
