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
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id("TransacDet_ID");
            $table->string('Categ_ID');
            $table->string('Transac_ID');
            $table->decimal('Qty', 8, 2);
            $table->decimal('Weight', 8, 2);
            $table->decimal('Price', 8, 2);
            // $table->timestamps('Datetime_taken');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};
