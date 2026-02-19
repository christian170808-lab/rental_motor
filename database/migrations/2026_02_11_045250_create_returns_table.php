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
        $table->id();

        $table->foreignId('booking_id')
              ->constrained()
              ->onDelete('cascade');

        $table->date('return_date');

        $table->integer('late_days')->default(0);

        $table->decimal('penalty', 12, 2)->default(0);

        $table->string('vehicle_condition');

        $table->timestamps();
    });
}


    // Membatalkan migrasi untuk menghapus tabel 'returns'
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};