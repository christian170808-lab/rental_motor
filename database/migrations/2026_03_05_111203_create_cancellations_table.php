<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
        {
            Schema::create('cancellations', function (Blueprint $table) {
        $table->id();
        $table->string('customer_name');
        $table->string('vehicle_name');
        $table->string('plate_number');
        $table->text('reason');
        $table->date('cancelled_date');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancellations');
    }
};
