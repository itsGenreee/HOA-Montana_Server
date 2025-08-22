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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            // User who made the reservation
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Facility name (hardcoded list in app, just store value here)
            $table->string('facility');

            // Reservation datetime range
            $table->date('date');  // Store the date of reservation
            $table->time('start_time');
            $table->time('end_time');

            // Optional fee column (nullable for now)
            $table->decimal('fee', 8, 2)->nullable();

            // Status (pending, confirmed, canceled)
            $table->enum('status', ['pending', 'confirmed', 'canceled'])
                  ->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
