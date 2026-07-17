<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Quotation;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;
        $invoiceIds = Invoice::forCompany($companyId)->pluck('id');
        $invoices = Invoice::with('payments')->forCompany($companyId)->get();
        $payments = InvoicePayment::whereIn('invoice_id', $invoiceIds)->get();
        $periodStart = now()->startOfMonth()->subMonths(5);
        $months = collect(CarbonPeriod::create($periodStart, '1 month', now()->startOfMonth()))
            ->map(fn (Carbon $date) => [
                'key' => $date->format('Y-m'),
                'label' => $date->format('M Y'),
            ]);
        $monthlyIssued = $months->map(fn (array $month) => (float) $invoices
            ->filter(fn (Invoice $invoice) => optional($invoice->issue_date)->format('Y-m') === $month['key'])
            ->sum('total'));
        $monthlyCollected = $months->map(fn (array $month) => (float) $payments
            ->filter(fn (InvoicePayment $payment) => optional($payment->paid_at)->format('Y-m') === $month['key'])
            ->sum('amount'));
        $outstanding = $invoices->sum(fn (Invoice $invoice) => max(0, (float) $invoice->total - (float) $invoice->payments->sum('amount')));
        $overdueInvoices = $invoices
            ->filter(fn (Invoice $invoice) => $invoice->due_date && $invoice->due_date->isPast() && ! in_array($invoice->status, ['paid', 'void'], true));
        $statusLabels = ['draft', 'sent', 'partial', 'paid', 'void'];
        $statusCounts = collect($statusLabels)->map(fn (string $status) => $invoices->where('status', $status)->count());
        $revenue = $payments->sum('amount');
        $issuedAmount = $invoices->sum('total');

        return view('dashboard.index', [
            'stats' => [
                [
                    'label' => 'Total Invoices',
                    'value' => $invoices->count(),
                    'meta' => 'All documents',
                    'href' => route('invoices.index'),
                ],
                [
                    'label' => 'Issued Amount',
                    'value' => 'Rp '.number_format((float) $issuedAmount, 0, ',', '.'),
                    'meta' => 'Invoice total',
                    'href' => route('invoices.index'),
                ],
                [
                    'label' => 'Collected Revenue',
                    'value' => 'Rp '.number_format((float) $revenue, 0, ',', '.'),
                    'meta' => 'Recorded payments',
                    'href' => route('invoices.index'),
                ],
                [
                    'label' => 'Outstanding',
                    'value' => 'Rp '.number_format((float) $outstanding, 0, ',', '.'),
                    'meta' => 'Unpaid balance',
                    'href' => route('invoices.index'),
                ],
                [
                    'label' => 'Overdue',
                    'value' => $overdueInvoices->count(),
                    'meta' => 'Past due invoices',
                    'href' => route('invoices.index'),
                ],
            ],
            'invoiceCount' => $invoices->count(),
            'quotationCount' => Quotation::forCompany($companyId)->count(),
            'revenue' => $revenue,
            'pendingPayments' => $invoices->whereIn('status', ['sent', 'partial'])->count(),
            'chartData' => [
                'months' => $months->pluck('label')->values(),
                'issued' => $monthlyIssued->values(),
                'collected' => $monthlyCollected->values(),
                'statusLabels' => collect($statusLabels)->map(fn (string $status) => str($status)->headline())->values(),
                'statusKeys' => $statusLabels,
                'statusCounts' => $statusCounts->values(),
            ],
            'recentInvoices' => Invoice::with('client')->forCompany($companyId)->latest()->limit(5)->get(),
        ]);
    }
}
