
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('amenity_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);     // e.g. 15 chairs
            $table->decimal('price', 8, 2)->nullable();       // snapshot: price * qty
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_amenities');
    }
};
