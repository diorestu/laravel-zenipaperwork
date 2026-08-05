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
            ->limit(10)
            ->get();

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
            'total_invoices' => Invoice::forCompany($companyId)->count(),
            'revenue_this_month' => (float) Invoice::forCompany($companyId)
                ->whereBetween('issue_date', [now()->startOfMonth(), now()->endOfMonth()])
                ->where('status', '!=', 'void')
                ->sum('total'),
            'unpaid_balance' => (float) $unpaidBalance,
            'overdue_count' => Invoice::forCompany($companyId)->overdue()->count(),
        ];

        return view('mobile.workspace', compact(
            'clients',
            'products',
            'bankAccounts',
            'recentInvoices',
            'recentQuotations',
            'stats'
        ));
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
