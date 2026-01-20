<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to use raw SQL to change ENUM to VARCHAR
        DB::statement("ALTER TABLE products MODIFY COLUMN category VARCHAR(100) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to ENUM (note: this may fail if data doesn't match enum values)
        DB::statement("ALTER TABLE products MODIFY COLUMN category ENUM('food', 'drink', 'dessert') NOT NULL");
    }
};
