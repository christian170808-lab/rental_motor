<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToReturnsTable extends Migration
{
    public function up()
    {
        Schema::table('returns', function (Blueprint $table) {
            // Menambahkan kolom yang kurang
            if (!Schema::hasColumn('returns', 'late_days')) {
                $table->integer('late_days')->default(0)->after('return_date');
            }
            if (!Schema::hasColumn('returns', 'penalty')) {
                $table->decimal('penalty', 10, 2)->default(0)->after('late_days');
            }
            if (!Schema::hasColumn('returns', 'vehicle_condition')) {
                $table->string('vehicle_condition')->after('penalty');
            }
        });
    }

    public function down()
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->dropColumn(['late_days', 'penalty', 'vehicle_condition']);
        });
    }
}