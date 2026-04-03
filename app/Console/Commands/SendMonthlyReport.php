<?php

namespace App\Console\Commands;

use App\Http\Controllers\ReportController;
use App\Mail\MonthlyFinancialReport;
use App\Models\Setting;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendMonthlyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:send-monthly {--force : Bypass enabled/date checks (for testing)}';

    protected $description = 'Dispatches monthly financial report emails natively evaluating settings attributes iteratively.';

    public function handle(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $settings = Setting::where('user_id', $user->id)->pluck('value', 'key');
            $enabled = $settings['reports_enabled'] ?? '0';
            $sendDate = $settings['reports_send_date'] ?? 'end_of_month';

            if (! $this->option('force')) {
                if ($enabled !== '1') {
                    $this->warn("Skipping {$user->email}: reports disabled.");

                    continue;
                }

                $isEndOfMonth = now()->toDateString() === now()->endOfMonth()->toDateString();
                $isStartOfMonth = now()->toDateString() === now()->startOfMonth()->toDateString();

                if (($sendDate === 'end_of_month' && ! $isEndOfMonth) ||
                    ($sendDate === 'start_of_month' && ! $isStartOfMonth)) {
                    $this->warn("Skipping {$user->email}: not dispatch day.");

                    continue;
                }
            }

            // Calculate period dynamically
            $periodLabel = ($sendDate === 'start_of_month')
                ? now()->subMonth()->format('F Y')
                : now()->format('F Y');

            $start = ($sendDate === 'start_of_month') ? now()->subMonth()->startOfMonth() : now()->startOfMonth();
            $end = ($sendDate === 'start_of_month') ? now()->subMonth()->endOfMonth() : now()->endOfMonth();

            $data = ReportController::getReportData($start, $end);
            $data['period'] = 'monthly';
            $data['startDate'] = $start;
            $data['endDate'] = $end;

            $pdf = Pdf::loadView('reports.pdf', $data);

            Mail::to($user->email)->send(
                new MonthlyFinancialReport($pdf->output(), $periodLabel)
            );

            $this->info("✓ Dispatched monthly report to {$user->email}");
        }
    }
}
