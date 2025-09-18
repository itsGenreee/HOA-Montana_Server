
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // e.g. Chairs, Tables, Projector
            $table->decimal('price', 8, 2); // price per unit (e.g. 20.00 per chair)
            $table->integer('max_quantity')->nullable(); // for chairs, tables etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amenities');
    }
};
