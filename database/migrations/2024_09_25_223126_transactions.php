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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id("Transac_ID");
            $table->string('Cust_ID');
            $table->string('Admin_ID');
            $table->timestamps('Transac_date');
            $table->string('Transac_status');
            $table->string('Tracking_number');
            $table->timestamps('Pickup_datetime');
            $table->timestamps('Delivery_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
