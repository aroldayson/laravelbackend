<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('admin_lname');
            $table->string('admin_fname');
            $table->string('admin_mname');
            $table->string('admin_image');
            $table->date('birthdate');  
            $table->string('phone_no'); 
            $table->string('address');
            $table->string('role');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
