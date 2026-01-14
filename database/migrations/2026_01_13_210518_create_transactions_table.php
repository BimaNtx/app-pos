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
            $table->id();
            $table->string('customer_name')->nullable();
            $table->string('transaction_code')->unique();
            $table->enum('order_type', ['dine_in', 'takeaway'])->default('dine_in');
            $table->string('table_number')->nullable();
            $table->enum('payment_method', ['cash', 'qris', 'debit'])->default('cash');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('amount_received', 15, 2)->nullable();
            $table->decimal('change_amount', 15, 2)->nullable();
            $table->timestamps();
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
