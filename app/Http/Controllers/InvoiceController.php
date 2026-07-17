<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Client;
use App\Models\BankAccount;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        if ($request->boolean('datatable')) {
            return $this->datatable($request, $companyId);
        }

        $editInvoice = null;
        if ($request->filled('edit')) {
            $editInvoice = Invoice::forCompany($companyId)->with(['client', 'items'])->find($request->input('edit'));
        }

        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();

        // 1. Total Penjualan Bulan Ini
        $totalInvoicedThisMonth = Invoice::forCompany($companyId)
            ->whereBetween('issue_date', [$startOfMonth, $endOfMonth])
            ->where('status', '!=', 'void')
            ->sum('total');

        // 2. Pembayaran Diterima Bulan Ini
        $paymentsReceivedThisMonth = \App\Models\InvoicePayment::whereHas('invoice', function ($q) use ($companyId): void {
                $q->forCompany($companyId);
            })
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // 3. Piutang Aktif
        $unpaidBalance = Invoice::forCompany($companyId)
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->get()
            ->sum('balance_due');

        // 4. Invoice Baru Minggu Ini
        $newInvoicesThisWeekCount = Invoice::forCompany($companyId)
            ->whereBetween('issue_date', [$startOfWeek, $endOfWeek])
            ->count();

        $invoiceStats = [
            [
                'label' => 'Penjualan Bulan Ini',
                'value' => 'Rp ' . number_format((float) $totalInvoicedThisMonth, 0, ',', '.'),
                'meta' => 'Invoice diterbitkan bulan ini',
            ],
            [
                'label' => 'Pembayaran Masuk Bulan Ini',
                'value' => 'Rp ' . number_format((float) $paymentsReceivedThisMonth, 0, ',', '.'),
                'meta' => 'Cash flow masuk bulan ini',
            ],
            [
                'label' => 'Piutang Aktif',
                'value' => 'Rp ' . number_format((float) $unpaidBalance, 0, ',', '.'),
                'meta' => 'Total tagihan belum terbayar',
            ],
            [
                'label' => 'Invoice Baru Minggu Ini',
                'value' => $newInvoicesThisWeekCount,
                'meta' => 'Invoice dibuat minggu ini',
            ],
        ];

        return view('invoices.index', $this->formData() + compact('editInvoice', 'invoiceStats'));
    }

    public function create()
    {
        return redirect()->route('invoices.index', ['modal' => 'create']);
    }

    public function store(StoreInvoiceRequest $request, InvoiceService $service)
    {
        $invoice = $service->create($request->user(), $request->validated());

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice dibuat.');
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        return view('invoices.show', [
            'invoice' => $invoice->load(['company', 'client', 'items', 'payments', 'creditNotes', 'expenses']),
        ]);
    }

    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        return redirect()->route('invoices.index', ['edit' => $invoice->id]);
    }

    public function update(StoreInvoiceRequest $request, Invoice $invoice, InvoiceService $service)
    {
        $this->authorize('update', $invoice);
        $service->update($invoice, $request->validated());

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice diperbarui.');
    }

    public function pdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        return Pdf::loadView('pdf.invoice', ['invoice' => $invoice->load(['company', 'client', 'items', 'payments', 'creditNotes', 'expenses'])])
            ->download($invoice->number.'.pdf');
    }

    public function receipt(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        return Pdf::loadView('pdf.receipt', ['invoice' => $invoice->load(['company', 'client', 'payments'])])
            ->download($invoice->number.'-receipt.pdf');
    }

    public function status(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        $data = $request->validate(['status' => ['required', 'in:draft,sent,partial,paid,void,overdue']]);
        $invoice->update($data);

        return back()->with('success', 'Status invoice diperbarui.');
    }

    public function send(Invoice $invoice, InvoiceService $service)
    {
        $this->authorize('update', $invoice);
        $service->markAsSent($invoice);

        return back()->with('success', 'Invoice ditandai sudah dikirim.');
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice dihapus.');
    }

    private function datatable(Request $request, int $companyId)
    {
        $search = trim((string) $request->input('search.value', ''));
        $start = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $baseQuery = Invoice::query()->forCompany($companyId);
        $recordsTotal = (clone $baseQuery)->count();

        $filteredQuery = (clone $baseQuery)
            ->with(['client'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('number', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($q) use ($search): void {
                            $q->where('name', 'like', "%{$search}%")
                              ->orWhere('company_name', 'like', "%{$search}%");
                        });
                });
            });

        $recordsFiltered = (clone $filteredQuery)->count();
        $invoices = $filteredQuery->latest()->skip($start)->take($length)->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $invoices->map(fn (Invoice $invoice) => [
                'number' => '<a href="'.route('invoices.show', $invoice).'" class="font-medium text-brand-600 hover:text-brand-700">'.e($invoice->number).'</a>',
                'client' => '<div><p class="font-medium text-gray-900 dark:text-white/90">'.e($invoice->client->name).'</p>'.($invoice->client->company_name ? '<p class="text-xs text-gray-500">'.e($invoice->client->company_name).'</p>' : '').'</div>',
                'total' => '<p class="font-medium text-gray-900 dark:text-white/90">Rp '.number_format((float) $invoice->total, 0, ',', '.').'</p>',
                'status' => view('invoices.partials.datatable-status', compact('invoice'))->render(),
                'date' => $invoice->issue_date?->format('d M Y'),
                'action' => view('invoices.partials.datatable-actions', compact('invoice'))->render(),
            ]),
        ]);
    }

    private function formData(): array
    {
        $companyId = auth()->user()->company_id;

        return [
            'clients' => Client::forCompany($companyId)->orderBy('name')->get(),
            'products' => Product::forCompany($companyId)->orderBy('name')->get(),
            'bankAccounts' => BankAccount::forCompany($companyId)->where('is_active', true)->get(),
        ];
    }
}
