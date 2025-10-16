<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            // Link to user and facility
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
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
            $table->enum('status', ['pending', 'confirmed', 'canceled', 'checked_in'])->default('pending');
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('staffs');
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('staffs');
            $table->text('cancellation_reason')->nullable();

            // Reservation Info for Event Place
            $table->string('event_type')->nullable();
            $table->integer('guest_count')->nullable();

            //Additional Reservation Info if Admin make Reservation
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();

            // Security & Payment
            $table->uuid('reservation_token')->unique();
            $table->text('digital_signature')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('payment_intent_id')->nullable();
            $table->enum('payment_status', ['pending', 'processing', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->datetime('payment_deadline')->nullable();
            $table->datetime('checked_in_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('staffs')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
