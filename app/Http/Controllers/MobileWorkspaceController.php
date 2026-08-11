<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MobileWorkspaceController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $clients = Client::forCompany($companyId)->orderBy('name')->get();
        $products = Product::forCompany($companyId)->orderBy('name')->get();
        $bankAccounts = BankAccount::forCompany($companyId)->where('is_active', true)->get();

        $recentInvoices = Invoice::with(['client', 'items', 'payments'])
            ->forCompany($companyId)
            ->latest()
            ->limit(5)
            ->get();

        $totalInvoicesCount = Invoice::forCompany($companyId)->count();

        $recentQuotations = Quotation::with(['client'])
            ->forCompany($companyId)
            ->latest()
            ->limit(10)
            ->get();

        $unpaidBalance = Invoice::forCompany($companyId)
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->get()
            ->sum('balance_due');

        $stats = [
            'total_invoices' => $totalInvoicesCount,
            'revenue_this_month' => (float) Invoice::forCompany($companyId)
                ->whereBetween('issue_date', [now()->startOfMonth(), now()->endOfMonth()])
                ->where('status', '!=', 'void')
                ->sum('total'),
            'unpaid_balance' => (float) $unpaidBalance,
            'overdue_count' => Invoice::forCompany($companyId)->overdue()->count(),
        ];

        $initialInvoices = $recentInvoices->map(fn($inv) => [
            'id' => $inv->id,
            'number' => $inv->number,
            'client_name' => $inv->client?->name ?? '-',
            'issue_date_formatted' => $inv->issue_date?->format('d M Y') ?? '-',
            'status' => $inv->status,
            'total_formatted' => 'Rp ' . number_format((float) $inv->total, 0, ',', '.'),
            'show_url' => route('mobile.invoices.show', $inv),
            'pdf_url' => route('invoices.pdf', $inv),
        ])->values();

        return view('mobile.workspace', compact(
            'clients',
            'products',
            'bankAccounts',
            'recentInvoices',
            'initialInvoices',
            'totalInvoicesCount',
            'recentQuotations',
            'stats'
        ));
    }

    /**
     * Incremental load for invoices tab (10 items per batch).
     */
    public function loadMoreInvoices(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $offset = (int) $request->input('offset', 5);
        $limit = (int) $request->input('limit', 10);

        $invoices = Invoice::with(['client'])
            ->forCompany($companyId)
            ->latest()
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(function ($inv) {
                return [
                    'id' => $inv->id,
                    'number' => $inv->number,
                    'client_name' => $inv->client?->name ?? '-',
                    'issue_date_formatted' => $inv->issue_date?->format('d M Y') ?? '-',
                    'status' => $inv->status,
                    'status_badge_html' => view('components.status-badge', ['status' => $inv->status])->render(),
                    'total_formatted' => 'Rp ' . number_format((float) $inv->total, 0, ',', '.'),
                    'show_url' => route('mobile.invoices.show', $inv),
                    'pdf_url' => route('invoices.pdf', $inv),
                    'public_token' => $inv->public_token,
                ];
            });

        $totalCount = Invoice::forCompany($companyId)->count();
        $nextOffset = $offset + $invoices->count();

        return response()->json([
            'data' => $invoices,
            'has_more' => $nextOffset < $totalCount,
            'next_offset' => $nextOffset,
            'total' => $totalCount,
        ]);
    }

    /**
     * Return JSON stats filtered by month/year for real-time period filter.
     */
    public function stats(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $periodStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $periodEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $revenue = (float) Invoice::forCompany($companyId)
            ->whereBetween('issue_date', [$periodStart, $periodEnd])
            ->where('status', '!=', 'void')
            ->sum('total');

        $unpaidBalance = (float) Invoice::forCompany($companyId)
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->get()
            ->sum('balance_due');

        $overdueCount = Invoice::forCompany($companyId)->overdue()->count();

        $invoiceCount = (int) Invoice::forCompany($companyId)
            ->whereBetween('issue_date', [$periodStart, $periodEnd])
            ->count();

        return response()->json([
            'revenue' => $revenue,
            'revenue_formatted' => 'Rp ' . number_format($revenue, 0, ',', '.'),
            'unpaid_balance' => $unpaidBalance,
            'unpaid_balance_formatted' => 'Rp ' . number_format($unpaidBalance, 0, ',', '.'),
            'overdue_count' => $overdueCount,
            'invoice_count' => $invoiceCount,
            'period_label' => $periodStart->translatedFormat('F Y'),
        ]);
    }
}
