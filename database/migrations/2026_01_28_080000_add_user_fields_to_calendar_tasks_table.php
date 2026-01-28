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
            // Check if assigned_to column doesn't exist before adding
            if (!Schema::hasColumn('calendar_tasks', 'assigned_to')) {
                $table->string('assigned_to')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_tasks', function (Blueprint $table) {
            $table->dropColumn(['assigned_to']);
        });
    }
};
