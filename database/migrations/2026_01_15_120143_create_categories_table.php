<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('🏷️');
            $table->string('color')->default('text-gray-700');
            $table->string('bg_color')->default('bg-gray-100');
            $table->timestamps();
        });

        // Insert default categories
        DB::table('categories')->insert([
            ['name' => 'Makanan', 'slug' => 'food', 'icon' => '🍚', 'color' => 'text-orange-700', 'bg_color' => 'bg-orange-100', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Minuman', 'slug' => 'drink', 'icon' => '🥤', 'color' => 'text-blue-700', 'bg_color' => 'bg-blue-100', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dessert', 'slug' => 'dessert', 'icon' => '🍰', 'color' => 'text-pink-700', 'bg_color' => 'bg-pink-100', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Add category_id to products table
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        // Migrate existing category data
        $categories = DB::table('categories')->pluck('id', 'slug');
        foreach ($categories as $slug => $id) {
            DB::table('products')->where('category', $slug)->update(['category_id' => $id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('categories');
    }
};
