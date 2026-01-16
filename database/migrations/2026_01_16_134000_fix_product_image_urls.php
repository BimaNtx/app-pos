<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Convert absolute image URLs to relative paths
     */
    public function up(): void
    {
        // Get all products with http:// URLs (localhost URLs that need fixing)
        $products = DB::table('products')
            ->where('image_url', 'like', 'http://%/storage/%')
            ->orWhere('image_url', 'like', 'https://%/storage/%')
            ->get();

        foreach ($products as $product) {
            // Extract just the relative path from the URL
            // e.g., "http://127.0.0.1:8000/storage/products/xxx.jpg" -> "products/xxx.jpg"
            if (preg_match('/\/storage\/(.+)$/', $product->image_url, $matches)) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['image_url' => $matches[1]]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reliably reverse this migration
    }
};
