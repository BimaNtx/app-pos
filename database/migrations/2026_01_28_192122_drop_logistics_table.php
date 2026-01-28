<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('logistics');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('logistics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit');
            $table->integer('stock')->default(0);
            $table->integer('minimum_stock')->default(10);
            $table->timestamps();
        });
    }
};
