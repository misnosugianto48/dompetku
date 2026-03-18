<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dompetku Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #6366f1; }
        .header h1 { font-size: 22px; color: #6366f1; margin-bottom: 4px; }
        .header p { color: #64748b; font-size: 12px; }
        .summary { display: table; width: 100%; margin-bottom: 20px; }
        .summary-item { display: table-cell; width: 25%; text-align: center; padding: 10px; border: 1px solid #e2e8f0; }
        .summary-item .label { color: #64748b; font-size: 10px; text-transform: uppercase; }
        .summary-item .value { font-size: 14px; font-weight: bold; margin-top: 3px; }
        .green { color: #10b981; }
        .red { color: #ef4444; }
        .section-title { font-size: 14px; font-weight: bold; margin: 20px 0 10px; padding-bottom: 5px; border-bottom: 1px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background: #f8fafc; color: #64748b; font-size: 10px; text-transform: uppercase; padding: 8px 10px; text-align: left; border-bottom: 2px solid #e2e8f0; }
        td { padding: 7px 10px; border-bottom: 1px solid #f1f5f9; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; text-align: center; color: #94a3b8; font-size: 9px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>💰 Dompetku Financial Report</h1>
        <p>{{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }} ({{ ucfirst($period) }})</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="label">Income</div>
            <div class="value green">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Expense</div>
            <div class="value red">-Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Net Flow</div>
            <div class="value {{ $netFlow >= 0 ? 'green' : 'red' }}">{{ $netFlow >= 0 ? '+' : '' }}Rp {{ number_format($netFlow, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Balance</div>
            <div class="value">Rp {{ number_format($totalBalance, 0, ',', '.') }}</div>
        </div>
    </div>

    @if($expenseByCategory->count())
    <div class="section-title">Expense Breakdown</div>
    <table>
        <thead><tr><th>Category</th><th class="text-right">Amount</th></tr></thead>
        <tbody>
            @foreach($expenseByCategory as $cat)
            <tr><td>{{ $cat['name'] }}</td><td class="text-right red">Rp {{ number_format($cat['total'], 0, ',', '.') }}</td></tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($incomeByCategory->count())
    <div class="section-title">Income Breakdown</div>
    <table>
        <thead><tr><th>Category</th><th class="text-right">Amount</th></tr></thead>
        <tbody>
            @foreach($incomeByCategory as $cat)
            <tr><td>{{ $cat['name'] }}</td><td class="text-right green">Rp {{ number_format($cat['total'], 0, ',', '.') }}</td></tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="section-title">All Transactions</div>
    <table>
        <thead>
            <tr><th>Date</th><th>Category</th><th>Account</th><th>Description</th><th class="text-right">Amount</th></tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
            <tr>
                <td>{{ $t->date->format('d M Y') }}</td>
                <td>{{ $t->category->name }}</td>
                <td>{{ $t->account->name }}</td>
                <td>{{ $t->description ?: '-' }}</td>
                <td class="text-right {{ $t->type === 'income' ? 'green' : 'red' }}">
                    {{ $t->type === 'income' ? '+' : '-' }}Rp {{ number_format($t->amount, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center; color:#94a3b8; padding:20px;">No transactions in this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($assets->count())
    <div class="section-title">Digital Assets</div>
    <table>
        <thead><tr><th>Name</th><th>Type</th><th>Qty</th><th class="text-right">Buy Price</th><th class="text-right">Current</th><th class="text-right">Value</th></tr></thead>
        <tbody>
            @foreach($assets as $a)
            <tr>
                <td>{{ $a->name }}</td>
                <td>{{ str_replace('_', ' ', ucfirst($a->type)) }}</td>
                <td>{{ number_format($a->quantity, 4) }}</td>
                <td class="text-right">Rp {{ number_format($a->purchase_price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($a->current_price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($a->quantity * $a->current_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">Generated by Dompetku — {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
