<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Menjalankan migrasi untuk membuat tabel 'returns'
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            // Foreign key menghubungkan ke tabel 'bookings'
            $table->foreignId('booking_id')->constrained('bookings');
            $table->date('return_date'); // Tanggal pengembalian kendaraan
            $table->integer('fine')->default(0); // Denda (jika ada keterlambatan/kerusakan)
            $table->text('notes')->nullable(); // Catatan tambahan mengenai kondisi kendaraan
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    // Membatalkan migrasi untuk menghapus tabel 'returns'
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};