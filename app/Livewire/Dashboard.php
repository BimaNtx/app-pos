<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    /**
     * Get today's total sales.
     */
    #[Computed]
    public function todaySales(): float
    {
        return Transaction::whereDate('created_at', today())
            ->sum('total_amount');
    }

    /**
     * Get today's transaction count.
     */
    #[Computed]
    public function todayTransactions(): int
    {
        return Transaction::whereDate('created_at', today())->count();
    }

    /**
     * Get best selling product today.
     */
    #[Computed]
    public function bestSeller(): ?array
    {
        $result = TransactionDetail::query()
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->whereDate('transactions.created_at', today())
            ->select('products.name', DB::raw('SUM(transaction_details.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->first();

        return $result ? ['name' => $result->name, 'qty' => $result->total_qty] : null;
    }

    /**
     * Get recent transactions.
     */
    #[Computed]
    public function recentTransactions()
    {
        return Transaction::with('details.product')
            ->latest()
            ->take(10)
            ->get();
    }

    /**
     * Get sales data for the last 7 days.
     */
    #[Computed]
    public function weeklySales(): array
    {
        $sales = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $total = Transaction::whereDate('created_at', $date)->sum('total_amount');
            $sales[] = [
                'date' => $date->format('D'),
                'total' => $total,
            ];
        }
        return $sales;
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
