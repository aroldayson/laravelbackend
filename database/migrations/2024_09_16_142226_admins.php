<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id("Admin_ID");
            $table->string('Admin_lname');
            $table->string('Admin_fname');
            $table->string('Admin_mname');
            $table->string('Admin_image');
            $table->date('Birthdate');
            $table->string('Phone_no');
            $table->string('Address');
            $table->string('Role');
            $table->string('Email')->unique();
            $table->string('Password');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
