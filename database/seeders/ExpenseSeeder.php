<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@kasir.app')->first();
        $userId = $adminUser?->id ?? 1;

        $expenses = [
            [
                'date' => now()->format('Y-m-d'),
                'category' => 'bahan_baku',
                'description' => 'Belanja bahan baku sayuran',
                'amount' => 500000,
                'created_by' => $userId,
            ],
            [
                'date' => now()->format('Y-m-d'),
                'category' => 'bahan_baku',
                'description' => 'Beli daging ayam dan sapi',
                'amount' => 750000,
                'created_by' => $userId,
            ],
            [
                'date' => now()->format('Y-m-d'),
                'category' => 'operasional',
                'description' => 'Bayar listrik bulan ini',
                'amount' => 350000,
                'created_by' => $userId,
            ],
            [
                'date' => now()->format('Y-m-d'),
                'category' => 'operasional',
                'description' => 'Beli gas LPG 3 tabung',
                'amount' => 150000,
                'created_by' => $userId,
            ],
            [
                'date' => now()->format('Y-m-d'),
                'category' => 'gaji',
                'description' => 'Gaji karyawan harian',
                'amount' => 200000,
                'created_by' => $userId,
            ],
            [
                'date' => now()->format('Y-m-d'),
                'category' => 'lainnya',
                'description' => 'Biaya kebersihan',
                'amount' => 50000,
                'created_by' => $userId,
            ],
        ];

        foreach ($expenses as $expense) {
            Expense::create($expense);
        }
    }
}
