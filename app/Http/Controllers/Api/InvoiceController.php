<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Support\ActivityNotifier;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InvoiceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $search = trim((string) $request->query('search', ''));

        $invoices = Invoice::query()
            ->forCompany($request->user()->company_id)
            ->with(['client'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->when($request->filled('payment_status'), function ($query) use ($request): void {
                match ($request->query('payment_status')) {
                    'unpaid' => $query->whereIn('status', ['sent', 'partial']),
                    'paid' => $query->where('status', 'paid'),
                    'overdue' => $query->overdue(),
                    default => null,
                };
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('number', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('company_name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('issue_date')
            ->latest()
            ->paginate($this->perPage($request));

        return InvoiceResource::collection($invoices);
    }

    public function store(StoreInvoiceRequest $request, InvoiceService $service): InvoiceResource
    {
        $invoice = $service->create($request->user(), $request->validated());

        ActivityNotifier::record($request->user(), 'Invoice baru dibuat', 'Invoice '.$invoice->number.' berhasil dibuat.');

        return new InvoiceResource($invoice);
    }

    public function show(Invoice $invoice): InvoiceResource
    {
        $this->authorize('view', $invoice);

        return new InvoiceResource($invoice->load(['client', 'items', 'payments', 'paymentTerms']));
    }

    public function update(StoreInvoiceRequest $request, Invoice $invoice, InvoiceService $service): InvoiceResource
    {
        $this->authorize('update', $invoice);

        return new InvoiceResource($service->update($invoice, $request->validated()));
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);

        $invoice->delete();

        return response()->json(['message' => 'Invoice dihapus.']);
    }

    private function perPage(Request $request): int
    {
        return min(max((int) $request->query('per_page', 15), 1), 100);
    }
}
