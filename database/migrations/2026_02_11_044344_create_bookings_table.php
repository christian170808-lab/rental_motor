<?php
// ============================================================
// MIGRATION: create_bookings_table
// ============================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->string('identity_number')->nullable(); // nomor KTP (opsional)
            $table->string('identity_card');               // path foto KTP
            $table->string('payment_proof');               // path bukti bayar
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_cost', 12, 2);
            $table->string('payment_status')->default('pending'); // pending, paid, completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
