<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Product;
use App\Models\Quotation;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;
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

        $outstanding = $invoices->sum('balance_due');
        $overdueInvoices = $invoices
            ->filter(fn (Invoice $invoice) => $invoice->due_date && $invoice->due_date->isPast() && ! in_array($invoice->status, ['paid', 'void'], true));

        $statusLabels = ['draft', 'sent', 'partial', 'paid', 'void'];
        $statusCounts = collect($statusLabels)->map(fn (string $status) => $invoices->where('status', $status)->count());
        $revenue = $payments->sum('amount');
        $issuedAmount = $invoices->sum('total');

        $recentInvoices = Invoice::with('client')->forCompany($companyId)->latest()->limit(5)->get();

        return response()->json([
            'stats' => [
                'total_invoices' => $invoices->count(),
                'issued_amount' => (float) $issuedAmount,
                'collected_revenue' => (float) $revenue,
                'outstanding_balance' => (float) $outstanding,
                'overdue_count' => $overdueInvoices->count(),
            ],
            'counts' => [
                'invoices' => $invoices->count(),
                'quotations' => Quotation::forCompany($companyId)->count(),
                'clients' => Client::forCompany($companyId)->count(),
                'products' => Product::forCompany($companyId)->count(),
            ],
            'chart_data' => [
                'months' => $months->pluck('label')->values(),
                'issued' => $monthlyIssued->values(),
                'collected' => $monthlyCollected->values(),
                'status_labels' => $statusLabels,
                'status_counts' => $statusCounts->values(),
            ],
            'recent_invoices' => InvoiceResource::collection($recentInvoices),
        ]);
    }
}
