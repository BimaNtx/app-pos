<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        
        if ($products->isEmpty()) {
            return;
        }

        // Create sample transactions for TODAY
        $transactions = [
            [
                'customer_name' => 'Budi Santoso',
                'order_type' => 'dine_in',
                'table_number' => '5',
                'payment_method' => 'cash',
            ],
            [
                'customer_name' => 'Siti Aminah',
                'order_type' => 'takeaway',
                'table_number' => null,
                'payment_method' => 'qris',
            ],
            [
                'customer_name' => 'Ahmad Wijaya',
                'order_type' => 'dine_in',
                'table_number' => '3',
                'payment_method' => 'cash',
            ],
            [
                'customer_name' => 'Dewi Lestari',
                'order_type' => 'takeaway',
                'table_number' => null,
                'payment_method' => 'debit',
            ],
            [
                'customer_name' => 'Rizky Pratama',
                'order_type' => 'dine_in',
                'table_number' => '7',
                'payment_method' => 'cash',
            ],
        ];

        foreach ($transactions as $index => $transactionData) {
            // Pick random products for this transaction
            $selectedProducts = $products->random(rand(2, 4));
            $subtotal = 0;
            $items = [];

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $subtotal += $product->price * $quantity;
                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price_at_time' => $product->price,
                    'note' => null,
                ];
            }

            // Calculate totals
            $tax = $subtotal * 0.1; // 10% tax
            $total = $subtotal + $tax;
            $amountReceived = ceil($total / 10000) * 10000; // Round up to nearest 10k

            // Create transaction with TODAY's timestamp
            $transaction = Transaction::create([
                'customer_name' => $transactionData['customer_name'],
                'transaction_code' => Transaction::generateTransactionCode(),
                'order_type' => $transactionData['order_type'],
                'table_number' => $transactionData['table_number'],
                'payment_method' => $transactionData['payment_method'],
                'discount_type' => null,
                'discount_value' => 0,
                'discount_amount' => 0,
                'total_amount' => $total,
                'amount_received' => $transactionData['payment_method'] === 'cash' ? $amountReceived : $total,
                'change_amount' => $transactionData['payment_method'] === 'cash' ? $amountReceived - $total : 0,
                'created_at' => now(), // Explicitly use TODAY
                'updated_at' => now(),
            ]);

            // Create transaction details
            foreach ($items as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price_at_time' => $item['price_at_time'],
                    'note' => $item['note'],
                ]);
            }
        }
    }
}
