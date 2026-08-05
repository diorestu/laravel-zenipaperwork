<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Resources\QuotationResource;
use App\Models\Quotation;
use App\Services\QuotationService;
use App\Support\ActivityNotifier;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class QuotationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $search = trim((string) $request->query('search', ''));

        $quotations = Quotation::query()
            ->forCompany($request->user()->company_id)
            ->with(['client'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
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

        return QuotationResource::collection($quotations);
    }

    public function store(StoreQuotationRequest $request, QuotationService $service): QuotationResource
    {
        $quotation = $service->create($request->user(), $request->validated());

        ActivityNotifier::record($request->user(), 'Penawaran baru dibuat', 'Penawaran '.$quotation->number.' berhasil dibuat.');

        return new QuotationResource($quotation);
    }

    public function show(Quotation $quotation): QuotationResource
    {
        $this->authorize('view', $quotation);

        return new QuotationResource($quotation->load(['client', 'items']));
    }

    public function update(StoreQuotationRequest $request, Quotation $quotation, QuotationService $service): QuotationResource
    {
        $this->authorize('update', $quotation);

        return new QuotationResource($service->update($quotation, $request->validated()));
    }

    public function destroy(Quotation $quotation)
    {
        $this->authorize('delete', $quotation);

        $quotation->delete();

        return response()->json(['message' => 'Penawaran dihapus.']);
    }

    private function perPage(Request $request): int
    {
        return min(max((int) $request->query('per_page', 15), 1), 100);
    }
}
