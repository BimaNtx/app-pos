<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function downloadPdf(string $period = 'today')
    {
        // Calculate date range based on period
        [$start, $end] = match ($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            default => [now()->startOfDay(), now()->endOfDay()],
        };

        // Period label for display
        $periodLabel = match ($period) {
            'today' => 'Hari Ini',
            'week' => 'Minggu Ini',
            'month' => 'Bulan Ini',
            'year' => 'Tahun Ini',
            default => 'Hari Ini',
        };

        // Get all transactions with details for the period
        $transactions = Transaction::with(['details.product'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate totals
        $totalSales = $transactions->sum('total_amount');
        $totalTransactions = $transactions->count();
        $averageOrder = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        // Get all expenses for the period
        $expenses = Expense::where('date', '>=', $start->toDateString())
            ->where('date', '<=', $end->toDateString())
            ->orderBy('date', 'desc')
            ->get();

        $totalExpenses = $expenses->sum('amount');
        $netProfit = $totalSales - $totalExpenses;

        // Top products
        $topProducts = TransactionDetail::query()
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

        // Sales by category
        $salesByCategory = TransactionDetail::query()
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->whereBetween('transactions.created_at', [$start, $end])
            ->select(
                'products.category',
                DB::raw('SUM(transaction_details.quantity * transaction_details.price_at_time) as total_revenue')
            )
            ->groupBy('products.category')
            ->get();

        // Expenses by category
        $expensesByCategory = Expense::query()
            ->where('date', '>=', $start->toDateString())
            ->where('date', '<=', $end->toDateString())
            ->select(
                'category',
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->get();

        $data = [
            'periodLabel' => $periodLabel,
            'dateRange' => $start->format('d M Y') . ' - ' . $end->format('d M Y'),
            'generatedAt' => now()->format('d M Y H:i'),
            'totalSales' => $totalSales,
            'totalTransactions' => $totalTransactions,
            'averageOrder' => $averageOrder,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'transactions' => $transactions,
            'expenses' => $expenses,
            'topProducts' => $topProducts,
            'salesByCategory' => $salesByCategory,
            'expensesByCategory' => $expensesByCategory,
            'categoryLabels' => Expense::CATEGORIES,
        ];

        $pdf = Pdf::loadView('pdf.report-pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'laporan-' . $period . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
