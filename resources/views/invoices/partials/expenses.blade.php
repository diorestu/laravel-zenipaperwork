<div class="rounded-lg border border-gray-200 bg-white p-5">
    <div class="flex items-center justify-between">
        <h2 class="font-semibold">Expenses &amp; Profit</h2>
        <button type="button" data-modal-target="expense-modal" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium">+ Add Expense</button>
    </div>
    @if($invoice->expenses->isEmpty())
        <p class="mt-3 text-sm text-gray-500">Belum ada biaya tercatat.</p>
    @else
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-left text-xs uppercase text-gray-500">
                        <th class="py-2">Date</th>
                        <th class="py-2">Description</th>
                        <th class="py-2">Category</th>
                        <th class="py-2 text-right">Amount</th>
                        <th class="py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->expenses as $expense)
                        <tr class="border-b">
                            <td class="py-2">{{ $expense->expense_date->format('d M Y') }}</td>
                            <td class="py-2">{{ $expense->description }}</td>
                            <td class="py-2 text-gray-500">{{ $expense->category ?? '—' }}</td>
                            <td class="py-2 text-right"><x-money :amount="$expense->amount" /></td>
                            <td class="py-2 text-right">
                                <form method="POST" action="{{ route('invoices.expenses.destroy', [$invoice, $expense]) }}" onsubmit="return confirm('Hapus biaya ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-xs text-error-600">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t">
                        <td colspan="3" class="py-2 text-right text-sm font-semibold">Total Expenses</td>
                        <td class="py-2 text-right text-sm font-semibold"><x-money :amount="$invoice->expense_total" /></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>

<div id="expense-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-lg bg-white p-5">
        <h3 class="font-semibold">Add Expense</h3>
        <form method="POST" action="{{ route('invoices.expenses.store', $invoice) }}" class="mt-3 space-y-3">
            @csrf
            <x-form.input name="description" label="Description" />
            <x-form.input name="category" label="Category (optional)" />
            <x-form.input name="amount" label="Amount" type="number" step="0.01" />
            <x-form.input name="expense_date" label="Date" type="date" :value="now()->toDateString()" />
            <x-form.input name="notes" label="Notes" />
            <div class="flex justify-end gap-2">
                <button type="button" data-modal-close class="rounded-md border border-gray-300 px-3 py-2 text-sm">Cancel</button>
                <button class="rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white">Save</button>
            </div>
        </form>
    </div>
</div>
