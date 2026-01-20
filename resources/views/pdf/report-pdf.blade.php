<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan {{ $periodLabel }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            padding: 15px 0;
            border-bottom: 2px solid #0d9488;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 20px;
            color: #0d9488;
            margin-bottom: 3px;
        }
        .header .subtitle {
            color: #666;
            font-size: 12px;
        }
        .header .date-info {
            margin-top: 5px;
            font-size: 9px;
            color: #888;
        }
        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #0d9488;
            padding: 6px 10px;
            background-color: #f0fdfa;
            border-left: 3px solid #0d9488;
            margin-bottom: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table th {
            background-color: #0d9488;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: 600;
            font-size: 9px;
        }
        table td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
        }
        table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-table td {
            padding: 10px;
            border: 1px solid #e5e7eb;
        }
        .summary-label {
            font-size: 9px;
            color: #666;
            margin-bottom: 2px;
        }
        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
        }
        .summary-value.positive {
            color: #059669;
        }
        .summary-value.negative {
            color: #dc2626;
        }
        .no-data {
            text-align: center;
            color: #9ca3af;
            padding: 15px;
            font-style: italic;
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
        }
        .total-row {
            font-weight: bold;
            background-color: #f0fdfa !important;
        }
        .total-row td {
            border-top: 2px solid #0d9488;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>LAPORAN PENJUALAN</h1>
        <div class="subtitle">Periode: {{ $periodLabel }}</div>
        <div class="date-info">
            {{ $dateRange }} | Digenerate: {{ $generatedAt }}
        </div>
    </div>

    {{-- Ringkasan Finansial --}}
    <div class="section">
        <div class="section-title">RINGKASAN FINANSIAL</div>
        <table class="summary-table">
            <tr>
                <td style="width: 33%;">
                    <div class="summary-label">Total Penjualan</div>
                    <div class="summary-value">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
                </td>
                <td style="width: 33%;">
                    <div class="summary-label">Total Transaksi</div>
                    <div class="summary-value">{{ number_format($totalTransactions) }} transaksi</div>
                </td>
                <td style="width: 34%;">
                    <div class="summary-label">Rata-rata Pesanan</div>
                    <div class="summary-value">Rp {{ number_format($averageOrder, 0, ',', '.') }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="summary-label">Total Pemasukan (dari transaksi)</div>
                    <div class="summary-value positive">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="summary-label">Total Pengeluaran (dari expenses)</div>
                    <div class="summary-value negative">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="summary-label">Laba Bersih</div>
                    <div class="summary-value {{ $netProfit >= 0 ? 'positive' : 'negative' }}">
                        {{ $netProfit >= 0 ? '' : '-' }}Rp {{ number_format(abs($netProfit), 0, ',', '.') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Detail Transaksi (Sumber Total Penjualan) --}}
    <div class="section">
        <div class="section-title">DETAIL TRANSAKSI (Sumber Total Penjualan: Rp {{ number_format($totalSales, 0, ',', '.') }})</div>
        @if($transactions->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th style="width: 80px;">Tanggal</th>
                        <th style="width: 70px;">ID Transaksi</th>
                        <th>Produk</th>
                        <th class="text-right" style="width: 80px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $index => $trx)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                            <td>#{{ $trx->id }}</td>
                            <td>
                                @foreach($trx->details as $detail)
                                    {{ $detail->product->name ?? 'Produk Dihapus' }} ({{ $detail->quantity }}x)@if(!$loop->last), @endif
                                @endforeach
                            </td>
                            <td class="text-right">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="4" class="text-right"><strong>TOTAL PENJUALAN</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($totalSales, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="no-data">Tidak ada transaksi pada periode ini</div>
        @endif
    </div>

    {{-- Detail Pengeluaran (Sumber Total Pengeluaran) --}}
    <div class="section">
        <div class="section-title">DETAIL PENGELUARAN (Sumber Total Pengeluaran: Rp {{ number_format($totalExpenses, 0, ',', '.') }})</div>
        @if($expenses->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th style="width: 80px;">Tanggal</th>
                        <th style="width: 100px;">Kategori</th>
                        <th>Deskripsi</th>
                        <th class="text-right" style="width: 100px;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $index => $exp)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($exp->date)->format('d/m/Y') }}</td>
                            <td>{{ $categoryLabels[$exp->category] ?? ucfirst($exp->category) }}</td>
                            <td>{{ $exp->description ?: '-' }}</td>
                            <td class="text-right">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="4" class="text-right"><strong>TOTAL PENGELUARAN</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($totalExpenses, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="no-data">Tidak ada pengeluaran pada periode ini</div>
        @endif
    </div>

    {{-- Rekap Pengeluaran per Kategori --}}
    @if($expensesByCategory->count() > 0)
        <div class="section">
            <div class="section-title">REKAP PENGELUARAN PER KATEGORI</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Kategori</th>
                        <th class="text-right" style="width: 120px;">Total</th>
                        <th class="text-right" style="width: 80px;">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalExp = $expensesByCategory->sum('total_amount') ?: 1; @endphp
                    @foreach($expensesByCategory as $index => $exp)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $categoryLabels[$exp->category] ?? ucfirst($exp->category) }}</td>
                            <td class="text-right">Rp {{ number_format($exp->total_amount, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format(($exp->total_amount / $totalExp) * 100, 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Produk Terlaris --}}
    <div class="section">
        <div class="section-title">PRODUK TERLARIS</div>
        @if($topProducts->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Nama Produk</th>
                        <th style="width: 80px;">Kategori</th>
                        <th class="text-right" style="width: 70px;">Qty Terjual</th>
                        <th class="text-right" style="width: 100px;">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProducts as $index => $product)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $product->name }}</td>
                            <td style="text-transform: capitalize;">{{ $product->category }}</td>
                            <td class="text-right">{{ number_format($product->total_qty) }}</td>
                            <td class="text-right">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">Tidak ada data penjualan produk</div>
        @endif
    </div>

    {{-- Penjualan per Kategori --}}
    @if($salesByCategory->count() > 0)
        <div class="section">
            <div class="section-title">PENJUALAN PER KATEGORI PRODUK</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Kategori</th>
                        <th class="text-right" style="width: 120px;">Total Pendapatan</th>
                        <th class="text-right" style="width: 80px;">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalCatRevenue = $salesByCategory->sum('total_revenue') ?: 1; @endphp
                    @foreach($salesByCategory as $index => $cat)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td style="text-transform: capitalize;">{{ $cat->category }}</td>
                            <td class="text-right">Rp {{ number_format($cat->total_revenue, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format(($cat->total_revenue / $totalCatRevenue) * 100, 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Laporan ini digenerate secara otomatis oleh sistem POS | {{ $generatedAt }}
    </div>
</body>
</html>