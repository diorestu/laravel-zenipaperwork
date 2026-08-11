<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Carbon\Carbon;

class FinancialReportService
{
    public function getCashFlowReport(Company $company, ?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : now()->startOfYear();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfDay();

        // 1. Inflow: Payments Received
        $payments = InvoicePayment::whereHas('invoice', function ($q) use ($company): void {
            $q->forCompany($company->id);
        })
        ->whereBetween('paid_at', [$start, $end])
        ->get();

        $totalInflow = (float) $payments->sum('amount');

        $inflowByMethod = $payments->groupBy('method')->map(function ($group) {
            return (float) $group->sum('amount');
        })->toArray();

        // 2. Outflow: Expenses
        $expenses = Expense::forCompany($company->id)
            ->whereBetween('date', [$start, $end])
            ->get();

        $totalOutflow = (float) $expenses->sum('amount');

        $outflowByCategory = $expenses->groupBy('category')->map(function ($group) {
            return (float) $group->sum('amount');
        })->toArray();

        $netCashFlow = $totalInflow - $totalOutflow;

        return [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'total_inflow' => $totalInflow,
            'total_outflow' => $totalOutflow,
            'net_cashflow' => $netCashFlow,
            'inflow_by_method' => $inflowByMethod,
            'outflow_by_category' => $outflowByCategory,
            'payments' => $payments,
            'expenses' => $expenses,
        ];
    }

    public function getProfitLossReport(Company $company, ?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : now()->startOfYear();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfDay();

        $invoices = Invoice::forCompany($company->id)
            ->whereBetween('issue_date', [$start, $end])
            ->where('status', '!=', 'void')
            ->get();

        $grossRevenue = (float) $invoices->sum('subtotal');
        $totalDiscount = (float) $invoices->sum('discount_amount');
        $netRevenue = $grossRevenue - $totalDiscount;

        $totalPpn = (float) $invoices->sum('tax_total');
        $totalPph = (float) $invoices->sum('pph_amount');

        $generalExpenses = (float) Expense::forCompany($company->id)
            ->whereBetween('date', [$start, $end])
            ->sum('amount');

        $totalExpenses = $generalExpenses;
        $netProfit = $netRevenue - $totalExpenses - $totalPph;

        return [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'gross_revenue' => $grossRevenue,
            'total_discount' => $totalDiscount,
            'net_revenue' => $netRevenue,
            'total_ppn' => $totalPpn,
            'total_pph' => $totalPph,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'invoices_count' => $invoices->count(),
        ];
    }

    public function getAgingAccountsReceivable(Company $company): array
    {
        $unpaidInvoices = Invoice::forCompany($company->id)
            ->with('client')
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->where('balance_due', '>', 0)
            ->get();

        $today = now()->startOfDay();

        $current = 0; // 0-30 days in future
        $overdue1to30 = 0;
        $overdue31to60 = 0;
        $overdue61to90 = 0;
        $overdue90plus = 0;

        $categorizedInvoices = [];

        foreach ($unpaidInvoices as $invoice) {
            $dueDate = $invoice->due_date ? Carbon::parse($invoice->due_date)->startOfDay() : $today;
            $balance = (float) $invoice->balance_due;

            if ($dueDate->gte($today)) {
                $category = 'current';
                $current += $balance;
            } else {
                $daysOverdue = (int) $dueDate->diffInDays($today);
                if ($daysOverdue <= 30) {
                    $category = '1_30';
                    $overdue1to30 += $balance;
                } elseif ($daysOverdue <= 60) {
                    $category = '31_60';
                    $overdue31to60 += $balance;
                } elseif ($daysOverdue <= 90) {
                    $category = '61_90';
                    $overdue61to90 += $balance;
                } else {
                    $category = '90_plus';
                    $overdue90plus += $balance;
                }
            }

            $categorizedInvoices[] = [
                'invoice' => $invoice,
                'category' => $category,
                'days_overdue' => $dueDate->lt($today) ? (int) $dueDate->diffInDays($today) : 0,
                'balance_due' => $balance,
            ];
        }

        $totalAR = $current + $overdue1to30 + $overdue31to60 + $overdue61to90 + $overdue90plus;

        return [
            'total_ar' => $totalAR,
            'current' => $current,
            'overdue_1_30' => $overdue1to30,
            'overdue_31_60' => $overdue31to60,
            'overdue_61_90' => $overdue61to90,
            'overdue_90_plus' => $overdue90plus,
            'categorized_invoices' => $categorizedInvoices,
        ];
    }

    public function getTaxReport(Company $company, ?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : now()->startOfYear();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfDay();

        $invoices = Invoice::forCompany($company->id)
            ->with('client')
            ->whereBetween('issue_date', [$start, $end])
            ->where('status', '!=', 'void')
            ->get();

        $totalDpp = (float) $invoices->sum(function ($inv) {
            return (float) $inv->subtotal - (float) $inv->discount_amount;
        });

        $totalPpn = (float) $invoices->sum('tax_total');
        $totalPph = (float) $invoices->sum('pph_amount');

        $taxSummary = [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'total_dpp' => $totalDpp,
            'total_ppn' => $totalPpn,
            'total_pph' => $totalPph,
            'invoices' => $invoices,
        ];

        return $taxSummary;
    }
}
