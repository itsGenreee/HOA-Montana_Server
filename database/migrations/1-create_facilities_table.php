<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->time('start_time')->nullable(); // only for hourly
            $table->time('end_time')->nullable();   // only for hourly
            $table->unsignedInteger('interval_minutes')->nullable(); // e.g. 60 mins for hourly
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
