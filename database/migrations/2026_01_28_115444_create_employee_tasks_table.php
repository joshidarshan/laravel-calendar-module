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
        Schema::create('employee_tasks', function (Blueprint $table) {
            $table->id();
              $table->string('employee_name'); // Or employee_id if you want foreign key
            $table->string('task_name');
            $table->tinyInteger('priority')->default(0); // 0-low,1-medium,2-high
            $table->integer('completed')->default(0);
            $table->integer('pending')->default(0);
            $table->integer('overdue')->default(0);
            $table->integer('in_progress')->default(0);
            $table->integer('delayed')->default(0);
            $table->date('task_date')->nullable(); // date of task
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_tasks');
    }
};
