<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facility_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');

            // Type of fee: base, shift, block
            $table->enum('type', ['base', 'shift', 'block'])->default('base');

            $table->decimal('fee', 8, 2); // facility fee
            $table->decimal('discounted_fee', 8, 2)->nullable();

            // Option for shift/block
            $table->time('start_time')->nullable(); // start of shift/block
            $table->time('end_time')->nullable();   // end of shift/block
            $table->string('name')->nullable();     // e.g., 'day', 'night' for shift, or block name

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_fees');
    }
};
