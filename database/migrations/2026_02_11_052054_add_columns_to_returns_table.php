<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToReturnsTable extends Migration
{
    // Menjalankan migrasi untuk menambahkan kolom baru ke tabel 'returns'
    public function up(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            // Menambahkan kolom jumlah hari keterlambatan jika belum ada
            if (!Schema::hasColumn('returns', 'late_days')) {
                $table->integer('late_days')->default(0)->after('return_date');
            }
            // Menambahkan kolom denda keterlambatan jika belum ada
            if (!Schema::hasColumn('returns', 'penalty')) {
                $table->decimal('penalty', 10, 2)->default(0)->after('late_days');
            }
            // Menambahkan kolom kondisi kendaraan saat dikembalikan jika belum ada
            if (!Schema::hasColumn('returns', 'vehicle_condition')) {
                $table->string('vehicle_condition')->after('penalty');
            }
        });
    }

    // Membatalkan migrasi untuk menghapus kolom yang ditambahkan
    public function down(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->dropColumn(['late_days', 'penalty', 'vehicle_condition']);
        });
    }
}