<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use App\Support\ActivityNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ExpenseWebController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        if ($request->boolean('datatable')) {
            return $this->datatable($request, $companyId);
        }

        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startOfYear = $now->copy()->startOfYear();

        $totalExpensesThisMonth = Expense::forCompany($companyId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $totalExpensesThisYear = Expense::forCompany($companyId)
            ->whereBetween('date', [$startOfYear, $now])
            ->sum('amount');

        $topCategory = Expense::forCompany($companyId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->selectRaw('category, SUM(amount) as total_amount')
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->first();

        $invoices = Invoice::forCompany($companyId)->latest()->get();

        return view('expenses.index', compact(
            'totalExpensesThisMonth',
            'totalExpensesThisYear',
            'topCategory',
            'invoices'
        ));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'invoice_id' => ['nullable', Rule::exists('invoices', 'id')->where('company_id', $companyId)],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('expenses', 'public');
        }

        $expense = Expense::create([
            'company_id' => $companyId,
            'invoice_id' => $validated['invoice_id'] ?? null,
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'description' => $validated['description'] ?? null,
            'receipt_path' => $receiptPath,
        ]);

        ActivityNotifier::record($request->user(), 'Pengeluaran Dicatat', 'Pengeluaran '.$expense->category.' sebesar Rp '.number_format((float) $expense->amount, 0, ',', '.').' berhasil dicatat.');

        return redirect()->route('expenses.index')->with('success', 'Catatan pengeluaran berhasil disimpan.');
    }

    public function update(Request $request, Expense $expense)
    {
        abort_unless($expense->company_id === auth()->user()->company_id, 403);

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'invoice_id' => ['nullable', Rule::exists('invoices', 'id')->where('company_id', auth()->user()->company_id)],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        if ($request->hasFile('receipt')) {
            if ($expense->receipt_path && Storage::disk('public')->exists($expense->receipt_path)) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            $validated['receipt_path'] = $request->file('receipt')->store('expenses', 'public');
        }

        $expense->update($validated);

        return redirect()->route('expenses.index')->with('success', 'Catatan pengeluaran berhasil diperbarui.');
    }

    public function destroy(Request $request, Expense $expense)
    {
        abort_unless($expense->company_id === auth()->user()->company_id, 403);

        if ($expense->receipt_path && Storage::disk('public')->exists($expense->receipt_path)) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Catatan pengeluaran berhasil dihapus.');
    }

    private function datatable(Request $request, int $companyId)
    {
        $query = Expense::forCompany($companyId)->with('invoice');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $recordsTotal = Expense::forCompany($companyId)->count();
        $recordsFiltered = (clone $query)->count();

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $expenses = $query->latest('date')->skip($start)->take($length)->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $expenses->map(fn (Expense $exp) => [
                'date' => $exp->date?->format('d M Y'),
                'category' => '<span class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-800 dark:bg-white/10 dark:text-gray-200">'.e($exp->category).'</span>',
                'description' => '<div><p class="font-medium text-gray-900 dark:text-white">'.e($exp->description ?? '-').'</p>'.($exp->invoice ? '<p class="text-xs text-brand-600 dark:text-brand-400">Terhubung: '.e($exp->invoice->number).'</p>' : '').'</div>',
                'amount' => '<span class="font-bold text-red-600 dark:text-red-400">Rp '.number_format((float) $exp->amount, 0, ',', '.').'</span>',
                'receipt' => $exp->receipt_path ? '<a href="'.Storage::disk('public')->url($exp->receipt_path).'" target="_blank" class="inline-flex items-center gap-1 text-xs font-semibold text-brand-600 hover:underline"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> Bukti Nota</a>' : '<span class="text-xs text-gray-400">-</span>',
                'action' => view('expenses.partials.actions', ['expense' => $exp])->render(),
            ]),
        ]);
    }
}
