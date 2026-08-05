<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Support\ActivityNotifier;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        if ($request->boolean('datatable')) {
            return $this->datatable($request, $companyId);
        }

        $productStats = [
            [
                'label' => 'Total Produk',
                'value' => Product::forCompany($companyId)->count(),
                'meta' => 'Semua produk/layanan',
            ],
            [
                'label' => 'Produk Aktif',
                'value' => Product::forCompany($companyId)->where('is_active', true)->count(),
                'meta' => 'Siap dipakai dokumen',
            ],
            [
                'label' => 'Harga Rata-rata',
                'value' => 'Rp '.number_format((float) Product::forCompany($companyId)->avg('price'), 0, ',', '.'),
                'meta' => 'Rata-rata harga dasar',
            ],
            [
                'label' => 'Sudah Dipakai',
                'value' => Product::forCompany($companyId)
                    ->where(function ($query): void {
                        $query->whereHas('invoiceItems')->orWhereHas('quotationItems');
                    })
                    ->count(),
                'meta' => 'Ada di invoice/penawaran',
            ],
        ];

        return view('products.index', compact('productStats'));
    }

    private function datatable(Request $request, int $companyId)
    {
        $search = trim((string) $request->input('search.value', ''));
        $start = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $baseQuery = Product::query()->forCompany($companyId);
        $recordsTotal = (clone $baseQuery)->count();

        $filteredQuery = (clone $baseQuery)
            ->withCount(['invoiceItems', 'quotationItems'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('unit', 'like', "%{$search}%");
                });
            });

        $recordsFiltered = (clone $filteredQuery)->count();
        $products = $filteredQuery->latest()->skip($start)->take($length)->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $products->map(fn (Product $product) => [
                'product' => view('products.partials.datatable-product', compact('product'))->render(),
                'price' => '<p class="font-medium text-gray-900 dark:text-white/90">Rp '.number_format((float) $product->price, 0, ',', '.').'</p><p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">per '.e($product->unit).'</p>',
                'usage' => '<div class="flex flex-wrap gap-2"><span class="rounded-full border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-700 dark:border-gray-700 dark:bg-white/5 dark:text-gray-300">'.$product->invoice_items_count.' item invoice</span><span class="rounded-full border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-700 dark:border-gray-700 dark:bg-white/5 dark:text-gray-300">'.$product->quotation_items_count.' item penawaran</span></div>',
                'status' => '<span class="inline-flex rounded-full border px-2 py-1 text-xs font-medium '.($product->is_active ? 'border-success-200 bg-success-50 text-success-700 dark:border-success-500/30 dark:bg-success-500/10 dark:text-success-400' : 'border-gray-200 bg-gray-50 text-gray-600 dark:border-gray-700 dark:bg-white/5 dark:text-gray-400').'">'.($product->is_active ? 'Aktif' : 'Nonaktif').'</span>',
                'updated' => $product->updated_at?->format('d M Y'),
                'action' => view('products.partials.datatable-actions', compact('product'))->render(),
            ]),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated() + ['company_id' => $request->user()->company_id, 'is_active' => $request->boolean('is_active', true)]);
        ActivityNotifier::record($request->user(), 'Produk baru dibuat', $product->name.' ditambahkan ke katalog.');

        if ($request->boolean('from_mobile') || str_contains($request->header('referer', ''), '/mobile')) {
            return redirect()->route('mobile.app')->with('success', 'Produk tersimpan.');
        }

        return back()->with('success', 'Produk tersimpan.');
    }

    public function update(StoreProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);
        $product->update($request->validated() + ['is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Produk diperbarui.');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();

        return back()->with('success', 'Produk dihapus.');
    }
}
