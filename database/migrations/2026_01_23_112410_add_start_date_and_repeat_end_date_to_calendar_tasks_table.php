<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('calendar_tasks', function (Blueprint $table) {
            // add start date for repeats and an optional repeat end date
            $table->date('start_date')->nullable()->after('task_datetime');
            $table->date('repeat_end_date')->nullable()->after('start_date');
        });

        // Backfill start_date from task_datetime for existing records (only date portion)
        DB::table('calendar_tasks')->whereNull('start_date')->update([
            'start_date' => DB::raw('DATE(task_datetime)')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_tasks', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'repeat_end_date']);
        });
    }
};