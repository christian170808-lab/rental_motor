<?php
// ============================================================
// MIGRATION: create_returns_table
// NOTE: Kolom late_days, penalty, vehicle_condition sudah include di sini.
//       Migration AddColumnsToReturnsTable tidak diperlukan lagi (dihapus).
// ============================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->date('return_date');
            $table->integer('late_days')->default(0);
            $table->decimal('penalty', 12, 2)->default(0);
            $table->string('vehicle_condition'); // Good, Minor Damage, Major Damage
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
