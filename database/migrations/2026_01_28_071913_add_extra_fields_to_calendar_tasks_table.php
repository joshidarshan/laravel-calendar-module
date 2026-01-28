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
        Schema::table('calendar_tasks', function (Blueprint $table) {
            //
              $table->enum('priority', ['low','medium','high','urgent'])->default('medium')->after('task_type');
            $table->unsignedBigInteger('user_id')->nullable()->after('title');
            $table->string('location')->nullable()->after('description');
            $table->enum('status', ['pending','in_progress','completed','overdue'])->default('pending')->after('priority');
            $table->integer('reminder_minutes')->nullable()->after('repeat_end_date');
            $table->json('tags')->nullable()->after('repeat_end_date');
            $table->json('attachments')->nullable()->after('description');

            // Foreign key if using users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_tasks', function (Blueprint $table) {
                        $table->dropForeign(['user_id']);
            $table->dropColumn(['priority','user_id','location','status','reminder_minutes','tags','attachments']);

        });
    }
};
