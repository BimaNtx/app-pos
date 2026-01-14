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
class Reports extends Component
{
    public string $period = 'today';

    public function setPeriod(string $period): void
    {
        $this->period = $period;
    }

    #[Computed]
    public function dateRange(): array
    {
        return match($this->period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            default => [now()->startOfDay(), now()->endOfDay()],
        };
    }

    #[Computed]
    public function totalSales(): float
    {
        [$start, $end] = $this->dateRange;
        return Transaction::whereBetween('created_at', [$start, $end])->sum('total_amount');
    }

    #[Computed]
    public function totalTransactions(): int
    {
        [$start, $end] = $this->dateRange;
        return Transaction::whereBetween('created_at', [$start, $end])->count();
    }

    #[Computed]
    public function averageOrder(): float
    {
        if ($this->totalTransactions === 0) return 0;
        return $this->totalSales / $this->totalTransactions;
    }

    #[Computed]
    public function topProducts(): \Illuminate\Support\Collection
    {
        [$start, $end] = $this->dateRange;
        
        return TransactionDetail::query()
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->whereBetween('transactions.created_at', [$start, $end])
            ->select(
                'products.name',
                'products.category',
                DB::raw('SUM(transaction_details.quantity) as total_qty'),
                DB::raw('SUM(transaction_details.quantity * transaction_details.price_at_time) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.category')
            ->orderByDesc('total_qty')
            ->take(10)
            ->get();
    }

    #[Computed]
    public function salesByCategory(): \Illuminate\Support\Collection
    {
        [$start, $end] = $this->dateRange;
        
        return TransactionDetail::query()
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->whereBetween('transactions.created_at', [$start, $end])
            ->select(
                'products.category',
                DB::raw('SUM(transaction_details.quantity * transaction_details.price_at_time) as total_revenue')
            )
            ->groupBy('products.category')
            ->get();
    }

    public function render()
    {
        return view('livewire.reports');
    }
}
