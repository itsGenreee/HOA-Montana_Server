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

            // Link to user and facility
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');


            // Reservation datetime range
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');

            // Pricing
            $table->decimal('facility_fee', 8, 2)->nullable();
            $table->decimal('amenities_fee', 8, 2)->nullable();
            $table->decimal('total_fee', 8, 2)->nullable();


            // Status
            $table->enum('status', ['pending', 'confirmed', 'canceled'])->default('pending');

            //Reservation Info for Event Place
            $table->string('event_type')->nullable();
            $table->integer('guest_count')->nullable();

            // Security & Payment
            $table->uuid('reservation_token')->unique();
            $table->text('digital_signature')->nullable();
            $table->string('payment_id')->nullable();

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
