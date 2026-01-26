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
        Schema::create('calendar_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('task_datetime');
            $table->enum('task_type', ['task','event','meeting','other'])->default('task');
            $table->enum('repeat_type', ['none','daily','weekly','monthly'])->default('none');
            // Store completed occurrences as array of objects: [{date: "YYYY-MM-DD", completed_at: "YYYY-MM-DD HH:MM:SS"}]
            $table->json('completed_occurrences')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_tasks');
    }
};
