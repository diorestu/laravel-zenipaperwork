<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Models\Client;
use App\Models\Invoice;
use App\Support\ActivityNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        if ($request->boolean('datatable')) {
            return $this->datatable($request, $companyId);
        }

        $clientStats = [
            [
                'label' => 'Total Klien',
                'value' => Client::forCompany($companyId)->count(),
                'meta' => 'Semua data klien',
            ],
            [
                'label' => 'Klien Aktif',
                'value' => Client::forCompany($companyId)
                    ->where(function ($query): void {
                        $query->whereHas('invoices')->orWhereHas('quotations');
                    })
                    ->count(),
                'meta' => 'Punya invoice/penawaran',
            ],
            [
                'label' => 'Nilai Invoice',
                'value' => 'Rp '.number_format((float) Invoice::forCompany($companyId)->sum('total'), 0, ',', '.'),
                'meta' => 'Total invoice klien',
            ],
            [
                'label' => 'Klien Baru',
                'value' => Client::forCompany($companyId)->where('created_at', '>=', now()->startOfMonth())->count(),
                'meta' => now()->translatedFormat('F Y'),
            ],
        ];

        return view('clients.index', compact('clientStats'));
    }

    private function datatable(Request $request, int $companyId)
    {
        $search = trim((string) $request->input('search.value', ''));
        $start = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $baseQuery = Client::query()->forCompany($companyId);
        $recordsTotal = (clone $baseQuery)->count();

        $filteredQuery = (clone $baseQuery)
            ->withCount(['invoices', 'quotations'])
            ->withSum('invoices', 'total')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            });

        $recordsFiltered = (clone $filteredQuery)->count();
        $clients = $filteredQuery->latest()->skip($start)->take($length)->get();
        $unpaidByClient = $this->unpaidInvoiceValueByClient($clients->pluck('id')->all(), $companyId);

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $clients->map(fn (Client $client) => [
                'checkbox' => '<input type="checkbox" name="ids[]" value="'.$client->id.'" class="dt-checkbox rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-700">',
                'client' => view('clients.partials.datatable-client', compact('client'))->render(),
                'contact' => '<div class="space-y-1"><p class="text-gray-700 dark:text-gray-300">'.e($client->email ?: '-').'</p><p class="text-xs text-gray-500 dark:text-gray-400">'.e($client->phone ?: 'Tidak ada telepon').'</p></div>',
                'documents' => '<div class="flex flex-wrap gap-2"><span class="rounded-full border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-700 dark:border-gray-700 dark:bg-white/5 dark:text-gray-300">'.$client->invoices_count.' invoice</span><span class="rounded-full border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-700 dark:border-gray-700 dark:bg-white/5 dark:text-gray-300">'.$client->quotations_count.' penawaran</span></div>',
                'invoice_value' => '<span class="font-medium text-gray-900 dark:text-white/90">Rp '.number_format((float) ($client->invoices_sum_total ?? 0), 0, ',', '.').'</span>',
                'unpaid_value' => '<span class="font-semibold text-error-600 dark:text-error-400">Rp '.number_format((float) ($unpaidByClient[$client->id] ?? 0), 0, ',', '.').'</span>',
                'action' => view('clients.partials.datatable-actions', compact('client'))->render(),
            ]),
        ]);
    }

    private function unpaidInvoiceValueByClient(array $clientIds, int $companyId): array
    {
        if ($clientIds === []) {
            return [];
        }

        return Invoice::query()
            ->forCompany($companyId)
            ->whereIn('client_id', $clientIds)
            ->whereNotIn('status', ['draft', 'paid', 'void'])
            ->select('client_id', DB::raw('SUM(balance_due) as unpaid_total'))
            ->groupBy('client_id')
            ->pluck('unpaid_total', 'client_id')
            ->map(fn ($value): float => (float) $value)
            ->all();
    }

    public function store(StoreClientRequest $request)
    {
        $client = Client::create($request->validated() + ['company_id' => $request->user()->company_id]);
        ActivityNotifier::record($request->user(), 'Klien baru dibuat', $client->name.' ditambahkan sebagai klien.');

        if ($request->boolean('from_mobile') || str_contains($request->header('referer', ''), '/mobile')) {
            return redirect()->route('mobile.app')->with('success', 'Klien tersimpan.');
        }

        return back()->with('success', 'Klien tersimpan.');
    }

    public function update(StoreClientRequest $request, Client $client)
    {
        $this->authorize('update', $client);
        $client->update($request->validated());

        return back()->with('success', 'Klien diperbarui.');
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);
        $client->delete();

        return back()->with('success', 'Klien dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'Tidak ada klien yang dipilih.');
        }

        $clients = Client::whereIn('id', $ids)->where('company_id', $request->user()->company_id)->get();
        
        foreach ($clients as $client) {
            $this->authorize('delete', $client);
            $client->delete();
        }

        return back()->with('success', count($clients) . ' klien berhasil dihapus.');
    }
}
