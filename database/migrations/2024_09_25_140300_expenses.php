<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id("Expense_ID");
            $table->string('Admin_ID');
            $table->decimal('Amount', 8, 2);
            $table->string('Desc_reason');
            $table->string('Receipt_filenameimg');
            $table->timestamps('Datetime_taken');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
