<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quotation;
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
}
