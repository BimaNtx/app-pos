<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Food items (5)
            [
                'name' => 'Nasi Goreng Spesial',
                'category' => 'food',
                'price' => 25000,
                'image_url' => 'https://placehold.co/400x300/e67e22/ffffff?text=Nasi+Goreng',
                'description' => 'Nasi goreng dengan telur, ayam, dan sayuran segar',
            ],
            [
                'name' => 'Ayam Bakar Madu',
                'category' => 'food',
                'price' => 35000,
                'image_url' => 'https://placehold.co/400x300/c0392b/ffffff?text=Ayam+Bakar',
                'description' => 'Ayam bakar dengan bumbu madu spesial',
            ],
            [
                'name' => 'Sate Ayam',
                'category' => 'food',
                'price' => 30000,
                'image_url' => 'https://placehold.co/400x300/d35400/ffffff?text=Sate+Ayam',
                'description' => '10 tusuk sate ayam dengan bumbu kacang',
            ],
            [
                'name' => 'Mie Goreng Jawa',
                'category' => 'food',
                'price' => 22000,
                'image_url' => 'https://placehold.co/400x300/f39c12/ffffff?text=Mie+Goreng',
                'description' => 'Mie goreng dengan bumbu khas Jawa',
            ],
            [
                'name' => 'Nasi Campur Bali',
                'category' => 'food',
                'price' => 32000,
                'image_url' => 'https://placehold.co/400x300/e74c3c/ffffff?text=Nasi+Campur',
                'description' => 'Nasi dengan lauk pauk khas Bali',
            ],

            // Drink items (5)
            [
                'name' => 'Es Teh Manis',
                'category' => 'drink',
                'price' => 8000,
                'image_url' => 'https://placehold.co/400x300/27ae60/ffffff?text=Es+Teh',
                'description' => 'Teh manis dingin yang menyegarkan',
            ],
            [
                'name' => 'Es Jeruk Segar',
                'category' => 'drink',
                'price' => 12000,
                'image_url' => 'https://placehold.co/400x300/f1c40f/ffffff?text=Es+Jeruk',
                'description' => 'Jus jeruk segar dengan es',
            ],
            [
                'name' => 'Kopi Hitam',
                'category' => 'drink',
                'price' => 10000,
                'image_url' => 'https://placehold.co/400x300/2c3e50/ffffff?text=Kopi+Hitam',
                'description' => 'Kopi hitam tubruk tradisional',
            ],
            [
                'name' => 'Jus Alpukat',
                'category' => 'drink',
                'price' => 18000,
                'image_url' => 'https://placehold.co/400x300/27ae60/ffffff?text=Jus+Alpukat',
                'description' => 'Jus alpukat segar dengan susu',
            ],
            [
                'name' => 'Es Kelapa Muda',
                'category' => 'drink',
                'price' => 15000,
                'image_url' => 'https://placehold.co/400x300/1abc9c/ffffff?text=Es+Kelapa',
                'description' => 'Air kelapa muda segar dengan daging kelapa',
            ],

            // Dessert items (5)
            [
                'name' => 'Es Cendol',
                'category' => 'dessert',
                'price' => 12000,
                'image_url' => 'https://placehold.co/400x300/16a085/ffffff?text=Es+Cendol',
                'description' => 'Cendol dengan santan dan gula merah',
            ],
            [
                'name' => 'Pisang Goreng',
                'category' => 'dessert',
                'price' => 15000,
                'image_url' => 'https://placehold.co/400x300/e67e22/ffffff?text=Pisang+Goreng',
                'description' => 'Pisang goreng crispy dengan topping keju/coklat',
            ],
            [
                'name' => 'Es Campur',
                'category' => 'dessert',
                'price' => 18000,
                'image_url' => 'https://placehold.co/400x300/9b59b6/ffffff?text=Es+Campur',
                'description' => 'Es campur dengan berbagai topping',
            ],
            [
                'name' => 'Klepon',
                'category' => 'dessert',
                'price' => 10000,
                'image_url' => 'https://placehold.co/400x300/2ecc71/ffffff?text=Klepon',
                'description' => 'Kue klepon isi gula merah (5 pcs)',
            ],
            [
                'name' => 'Dadar Gulung',
                'category' => 'dessert',
                'price' => 12000,
                'image_url' => 'https://placehold.co/400x300/3498db/ffffff?text=Dadar+Gulung',
                'description' => 'Dadar gulung isi kelapa manis (3 pcs)',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
