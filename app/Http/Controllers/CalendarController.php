<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        // Parse month and year from request, or default to current month and year
        $month = $request->integer('month', (int) now()->format('m'));
        $year = $request->integer('year', (int) now()->format('Y'));

        // Clamp month values to 1-12 and year to reasonable ranges
        if ($month < 1 || $month > 12) {
            $month = (int) now()->format('m');
        }
        if ($year < 2000 || $year > 2100) {
            $year = (int) now()->format('Y');
        }

        $currentDate = Carbon::createFromDate($year, $month, 1)->startOfDay();

        // Calculate the grid dates
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();

        // Calendar grid starts on Monday and ends on Sunday. Expand to full weeks
        $gridStart = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        // Fetch invoices in this range
        $invoices = Invoice::with('client')
            ->forCompany($companyId)
            ->whereBetween('due_date', [$gridStart->toDateString(), $gridEnd->toDateString()])
            ->get();

        // Group by due date string
        $invoicesByDate = $invoices->groupBy(fn (Invoice $inv) => $inv->due_date?->toDateString());

        // Generate all dates in the grid
        $period = CarbonPeriod::create($gridStart, $gridEnd);
        $days = [];

        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            $days[] = [
                'date' => $date->copy(),
                'date_string' => $dateStr,
                'day_number' => $date->day,
                'is_current_month' => $date->month === $currentDate->month,
                'is_today' => $date->isToday(),
                'invoices' => $invoicesByDate->get($dateStr, collect()),
            ];
        }

        // Navigation URLs
        $prevDate = $currentDate->copy()->subMonth();
        $nextDate = $currentDate->copy()->addMonth();

        $prevMonthUrl = route('calendar', ['month' => $prevDate->month, 'year' => $prevDate->year]);
        $nextMonthUrl = route('calendar', ['month' => $nextDate->month, 'year' => $nextDate->year]);
        $todayUrl = route('calendar', ['month' => now()->month, 'year' => now()->year]);

        return view('pages.calender', [
            'days' => $days,
            'currentDate' => $currentDate,
            'prevMonthUrl' => $prevMonthUrl,
            'nextMonthUrl' => $nextMonthUrl,
            'todayUrl' => $todayUrl,
            'selectedMonth' => $month,
            'selectedYear' => $year,
        ]);
    }

    public function sync(Request $request)
    {
        $companyId = auth()->user()->company_id;

        // Perform status check and sync
        $invoices = Invoice::forCompany($companyId)
            ->whereIn('status', ['sent', 'partial'])
            ->get();

        $updatedCount = 0;
        foreach ($invoices as $invoice) {
            $paid = (float) $invoice->payments()->sum('amount');
            $creditNote = (float) $invoice->creditNotes()->where('status', 'applied')->sum('amount');
            $total = (float) $invoice->total;

            $newStatus = match (true) {
                $paid + $creditNote >= $total && $total > 0 => 'paid',
                $paid > 0 || $creditNote > 0 => 'partial',
                default => $invoice->status,
            };

            if ($newStatus !== $invoice->status) {
                $invoice->update(['status' => $newStatus]);
                $updatedCount++;
            }
        }

        // Add latency simulation (800ms) for a premium feedback feel
        usleep(800000);

        return response()->json([
            'success' => true,
            'message' => 'Data invoice berhasil disinkronkan. ' . ($updatedCount > 0 ? "{$updatedCount} status invoice diperbarui." : "Semua data sudah up to date."),
        ]);
    }
}
