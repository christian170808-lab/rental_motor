<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Menjalankan migrasi untuk membuat tabel 'bookings'
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            // Foreign key menghubungkan ke tabel 'vehicles'
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->string('customer_name'); // Nama pelanggan
            $table->string('identity_card'); // Nama file foto KTP
            $table->date('start_date'); // Tanggal mulai sewa
            $table->date('end_date'); // Tanggal selesai sewa
            $table->decimal('total_cost', 10, 2); // Total biaya sewa
            $table->string('payment_status'); // Status pembayaran (misal: 'pending', 'completed')
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    // Membatalkan migrasi untuk menghapus tabel 'bookings'
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};