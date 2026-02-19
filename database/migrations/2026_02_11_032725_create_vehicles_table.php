<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Menjalankan migrasi untuk membuat tabel 'vehicles'
    public function up()
{
    Schema::create('vehicles', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('type'); // skuter, sport, trail
        $table->string('plate_number')->unique();
        $table->decimal('price_per_day', 12, 2);
        $table->string('status')->default('available'); // available, rented, maintenance
        $table->string('image')->nullable();
        $table->timestamps();
    });
}


    // Membatalkan migrasi untuk menghapus tabel 'vehicles'
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};