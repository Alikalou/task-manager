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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // The log activity stream is dedicated to a project.
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();

            // The user id here to specify who did an activity, but it can be nullable.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // What happened?
            $table->string('action');

            // To what entity the event was applied, subject.
            $table->nullableMorphs('subject');

            // Human friendly sentence.
            $table->string('description');

            // Extra info like old/new values
            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
