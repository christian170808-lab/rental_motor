<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('returns', function (Blueprint $table) {
        $table->id();
        $table->foreignId('booking_id')->constrained('bookings'); // Terhubung ke bookings
        $table->date('return_date');
        $table->integer('fine')->default(0); // Denda jika ada
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
