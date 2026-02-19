<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();

        // Relasi ke customer
        $table->foreignId('customer_id')
              ->constrained()
              ->onDelete('cascade');

        // Relasi ke vehicle
        $table->foreignId('vehicle_id')
              ->constrained()
              ->onDelete('cascade');

        $table->string('identity_card');
        $table->string('payment_proof');

        $table->date('start_date');
        $table->date('end_date');

        $table->decimal('total_cost', 12, 2);

        $table->string('payment_status')
              ->default('pending'); // pending, paid, completed

        $table->timestamps();
    });
}


    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};