<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuotationRequest;
use App\Models\Client;
use App\Models\BankAccount;
use App\Models\Product;
use App\Models\Quotation;
use App\Services\InvoiceService;
use App\Services\QuotationService;
use App\Support\ActivityNotifier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        if ($request->boolean('datatable')) {
            return $this->datatable($request, $companyId);
        }

        $editQuotation = null;
        if ($request->filled('edit')) {
            $editQuotation = Quotation::forCompany($companyId)->with(['client', 'items'])->find($request->input('edit'));
        }

        return view('quotations.index', $this->formData() + compact('editQuotation'));
    }

    public function create()
    {
        return redirect()->route('quotations.index', ['modal' => 'create']);
    }

    public function store(StoreQuotationRequest $request, QuotationService $service)
    {
        $quotation = $service->create($request->user(), $request->validated());
        ActivityNotifier::record($request->user(), 'Penawaran baru dibuat', 'Penawaran '.$quotation->number.' berhasil dibuat.');

        return redirect()->route('quotations.show', $quotation)->with('success', 'Penawaran dibuat.');
    }

    public function show(Quotation $quotation)
    {
        $this->authorize('view', $quotation);

        return view('quotations.show', ['quotation' => $quotation->load(['company', 'client', 'items'])]);
    }

    public function edit(Quotation $quotation)
    {
        $this->authorize('update', $quotation);

        return redirect()->route('quotations.index', ['edit' => $quotation->id]);
    }

    public function update(StoreQuotationRequest $request, Quotation $quotation, QuotationService $service)
    {
        $this->authorize('update', $quotation);
        $service->update($quotation, $request->validated());

        return redirect()->route('quotations.show', $quotation)->with('success', 'Penawaran diperbarui.');
    }

    public function status(Request $request, Quotation $quotation, InvoiceService $invoiceService)
    {
        $this->authorize('update', $quotation);
        $data = $request->validate(['status' => ['required', 'in:approved,rejected,sent,draft']]);

        if ($data['status'] === 'approved') {
            $invoiceNumber = 'INV-' . now()->format('Ymd-His');
            $exists = \App\Models\Invoice::where('company_id', $quotation->company_id)->where('number', $invoiceNumber)->exists();
            if ($exists) {
                $invoiceNumber .= '-' . rand(10, 99);
            }

            $invoice = $invoiceService->convertQuotation($quotation->load('items'), $invoiceNumber);
            ActivityNotifier::record($request->user(), 'Invoice baru dibuat', 'Invoice '.$invoice->number.' dibuat dari penawaran '.$quotation->number.'.');

            return redirect()->route('invoices.show', $invoice)->with('success', 'Penawaran disetujui dan otomatis dikonversi menjadi invoice.');
        }

        $quotation->update($data);

        return back()->with('success', 'Status penawaran diperbarui.');
    }

    public function convert(Request $request, Quotation $quotation, InvoiceService $service)
    {
        $this->authorize('update', $quotation);
        $data = $request->validate(['number' => ['required', 'string', 'max:100']]);
        abort_unless(in_array($quotation->status, ['approved', 'sent'], true), 422);

        $invoice = $service->convertQuotation($quotation->load('items'), $data['number']);
        ActivityNotifier::record($request->user(), 'Invoice baru dibuat', 'Invoice '.$invoice->number.' dibuat dari penawaran '.$quotation->number.'.');

        return redirect()->route('invoices.show', $invoice)->with('success', 'Penawaran dikonversi ke invoice.');
    }

    public function pdf(Quotation $quotation)
    {
        $this->authorize('view', $quotation);

        return Pdf::loadView('pdf.quotation', ['quotation' => $quotation->load(['company', 'client', 'items'])])
            ->download($quotation->number.'.pdf');
    }

    public function destroy(Quotation $quotation)
    {
        $this->authorize('delete', $quotation);
        $quotation->delete();

        return redirect()->route('quotations.index')->with('success', 'Penawaran dihapus.');
    }

    private function datatable(Request $request, int $companyId)
    {
        $search = trim((string) $request->input('search.value', ''));
        $start = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $baseQuery = Quotation::query()->forCompany($companyId)->whereNotIn('status', ['approved', 'converted']);
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
        $quotations = $filteredQuery->latest()->skip($start)->take($length)->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $quotations->map(fn (Quotation $quotation) => [
                'number' => '<a href="'.route('quotations.show', $quotation).'" class="font-medium text-brand-600 hover:text-brand-700">'.e($quotation->number).'</a>',
                'client' => '<div><p class="font-medium text-gray-900 dark:text-white/90">'.e($quotation->client->name).'</p>'.($quotation->client->company_name ? '<p class="text-xs text-gray-500">'.e($quotation->client->company_name).'</p>' : '').'</div>',
                'total' => '<p class="font-medium text-gray-900 dark:text-white/90">Rp '.number_format((float) $quotation->total, 0, ',', '.').'</p>',
                'status' => view('quotations.partials.datatable-status', compact('quotation'))->render(),
                'date' => $quotation->issue_date?->format('d M Y'),
                'action' => view('quotations.partials.datatable-actions', compact('quotation'))->render(),
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
