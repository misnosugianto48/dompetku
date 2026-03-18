<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Asset;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        [$start, $end] = $this->resolveDateRange($period, $startDate, $endDate);

        $data = $this->getReportData($start, $end);
        $data['period'] = $period;
        $data['startDate'] = $start;
        $data['endDate'] = $end;

        return view('reports.index', $data);
    }

    public function exportPdf(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        [$start, $end] = $this->resolveDateRange($period, $startDate, $endDate);

        $data = $this->getReportData($start, $end);
        $data['period'] = $period;
        $data['startDate'] = $start;
        $data['endDate'] = $end;

        $pdf = Pdf::loadView('reports.pdf', $data);

        return $pdf->download('dompetku-report-'.$start->format('Y-m-d').'.pdf');
    }

    /**
     * @return array{Carbon, Carbon}
     */
    private function resolveDateRange(string $period, ?string $startDate, ?string $endDate): array
    {
        if ($startDate && $endDate) {
            return [Carbon::parse($startDate), Carbon::parse($endDate)];
        }

        return match ($period) {
            'daily' => [Carbon::today(), Carbon::today()->endOfDay()],
            'weekly' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'monthly' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'yearly' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function getReportData(Carbon $start, Carbon $end): array
    {
        $transactions = Transaction::with(['account', 'category'])
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->latest('date')
            ->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $netFlow = $totalIncome - $totalExpense;

        $expenseByCategory = $transactions->where('type', 'expense')
            ->groupBy('category_id')
            ->map(fn ($group) => [
                'name' => $group->first()->category->name,
                'color' => $group->first()->category->color,
                'total' => $group->sum('amount'),
            ])
            ->sortByDesc('total')
            ->values();

        $incomeByCategory = $transactions->where('type', 'income')
            ->groupBy('category_id')
            ->map(fn ($group) => [
                'name' => $group->first()->category->name,
                'color' => $group->first()->category->color,
                'total' => $group->sum('amount'),
            ])
            ->sortByDesc('total')
            ->values();

        $accounts = Account::all();
        $assets = Asset::all();
        $totalBalance = $accounts->sum('balance');
        $totalAssetValue = $assets->sum(fn ($a) => $a->quantity * $a->current_price);

        return compact(
            'transactions',
            'totalIncome',
            'totalExpense',
            'netFlow',
            'expenseByCategory',
            'incomeByCategory',
            'accounts',
            'assets',
            'totalBalance',
            'totalAssetValue'
        );
    }
}
