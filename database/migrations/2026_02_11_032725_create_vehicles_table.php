<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Menjalankan migrasi untuk membuat tabel 'vehicles'
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->string('name'); // Nama kendaraan
            $table->string('plate_number'); // Nomor plat kendaraan
            $table->string('status'); // Status kendaraan (misal: 'available', 'rented')
            $table->string('image')->nullable(); // Path file gambar kendaraan (opsional)
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    // Membatalkan migrasi untuk menghapus tabel 'vehicles'
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};