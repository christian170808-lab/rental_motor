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
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('vehicle_id')->constrained('vehicles'); // Menghubungkan ke tabel vehicles
        $table->string('customer_name');
        $table->string('identity_card');
        $table->date('start_date');
        $table->date('end_date');
        $table->decimal('total_cost', 10, 2);
        $table->string('payment_status');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
