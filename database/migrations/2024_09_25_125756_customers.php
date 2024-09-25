<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id("Cust_ID");
            $table->string('Cust_lname');
            $table->string('Cust_fname');
            $table->string('Cust_mname');
            $table->string('Cust_phoneno');
            $table->string('Cust_address');
            $table->string('Cust_email')->unique();
            $table->string('Cust_password');
            $table->string('Cust_image');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
