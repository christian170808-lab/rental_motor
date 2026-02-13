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
    Schema::table('returns', function (Blueprint $table) {
        // Pastikan kolom ini ada
        if (!Schema::hasColumn('returns', 'late_days')) {
            $table->integer('late_days')->default(0);
        }
        if (!Schema::hasColumn('returns', 'penalty')) {
            $table->decimal('penalty', 10, 2)->default(0);
        }
        if (!Schema::hasColumn('returns', 'vehicle_condition')) {
            $table->string('vehicle_condition')->after('penalty');
        }
    });
}

public function down()
{
    Schema::table('returns', function (Blueprint $table) {
        $table->dropColumn(['late_days', 'penalty']);
    });
}
};
