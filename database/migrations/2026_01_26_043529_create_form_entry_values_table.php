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
        Schema::create('form_entry_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_entry_id')
              ->constrained('form_entries') 
              ->cascadeOnDelete();

        $table->foreignId('form_field_id')
              ->constrained('form_fields')
              ->cascadeOnDelete();

        $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_entry_values');
    }
};
