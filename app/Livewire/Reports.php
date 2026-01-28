<?php

namespace App\Livewire;

use App\Models\Expense;
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
        return match ($this->period) {
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
        
        // Exclude soft-deleted transactions
        return Transaction::whereNull('deleted_at')
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount') ?? 0;
    }

    #[Computed]
    public function totalTransactions(): int
    {
        [$start, $end] = $this->dateRange;
        
        // Exclude soft-deleted transactions
        return Transaction::whereNull('deleted_at')
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    #[Computed]
    public function averageOrder(): float
    {
        $totalTransactions = $this->totalTransactions;
        
        // Handle division by zero
        if ($totalTransactions === 0) {
            return 0;
        }
        
        return $this->totalSales / $totalTransactions;
    }

    #[Computed]
    public function totalExpenses(): float
    {
        [$start, $end] = $this->dateRange;
        
        return Expense::whereDate('date', '>=', $start->toDateString())
            ->whereDate('date', '<=', $end->toDateString())
            ->sum('amount') ?? 0;
    }

    #[Computed]
    public function netProfit(): float
    {
        return $this->totalSales - $this->totalExpenses;
    }

    #[Computed]
    public function expensesByCategory(): \Illuminate\Support\Collection
    {
        [$start, $end] = $this->dateRange;

        return Expense::query()
            ->whereDate('date', '>=', $start->toDateString())
            ->whereDate('date', '<=', $end->toDateString())
            ->select(
                DB::raw("COALESCE(category, 'Lainnya') as category"),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->get();
    }

    #[Computed]
    public function topProducts(): \Illuminate\Support\Collection
    {
        [$start, $end] = $this->dateRange;

        return TransactionDetail::query()
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            // Use LEFT JOIN to include products that may have been deleted
            ->leftJoin('products', 'transaction_details.product_id', '=', 'products.id')
            ->whereBetween('transactions.created_at', [$start, $end])
            // CRITICAL: Exclude soft-deleted transactions
            ->whereNull('transactions.deleted_at')
            ->select(
                // Handle null product names (deleted products)
                DB::raw("COALESCE(products.name, 'Produk Tidak Ditemukan') as name"),
                DB::raw("COALESCE(products.category, 'Tanpa Kategori') as category"),
                DB::raw('SUM(transaction_details.quantity) as total_qty'),
                DB::raw('SUM(transaction_details.quantity * transaction_details.price_at_time) as total_revenue')
            )
            ->groupBy('transaction_details.product_id', 'products.name', 'products.category')
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
            // Use LEFT JOIN to include products that may have been deleted
            ->leftJoin('products', 'transaction_details.product_id', '=', 'products.id')
            ->whereBetween('transactions.created_at', [$start, $end])
            // CRITICAL: Exclude soft-deleted transactions
            ->whereNull('transactions.deleted_at')
            ->select(
                // Handle null categories
                DB::raw("COALESCE(products.category, 'Tanpa Kategori') as category"),
                DB::raw('SUM(transaction_details.quantity * transaction_details.price_at_time) as total_revenue')
            )
            ->groupBy(DB::raw("COALESCE(products.category, 'Tanpa Kategori')"))
            ->orderByDesc('total_revenue')
            ->get();
    }

    /**
     * Get total revenue percentage by category for chart display
     */
    #[Computed]
    public function categoryPercentages(): array
    {
        $salesByCategory = $this->salesByCategory;
        $totalRevenue = $salesByCategory->sum('total_revenue');
        
        // Handle division by zero
        if ($totalRevenue == 0) {
            return [];
        }
        
        return $salesByCategory->map(function ($item) use ($totalRevenue) {
            return [
                'category' => $item->category,
                'total_revenue' => $item->total_revenue,
                'percentage' => round(($item->total_revenue / $totalRevenue) * 100, 1),
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.reports', [
            'categoryLabels' => Expense::CATEGORIES,
        ]);
    }
}
