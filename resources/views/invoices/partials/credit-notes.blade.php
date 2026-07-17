<div class="rounded-lg border border-gray-200 bg-white p-5">
    <div class="flex items-center justify-between">
        <h2 class="font-semibold">Credit Notes</h2>
        <button type="button" data-modal-target="credit-note-modal" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium">+ Issue Credit Note</button>
    </div>
    @if($invoice->creditNotes->isEmpty())
        <p class="mt-3 text-sm text-gray-500">Belum ada credit note.</p>
    @else
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-left text-xs uppercase text-gray-500">
                        <th class="py-2">Number</th>
                        <th class="py-2">Date</th>
                        <th class="py-2">Reason</th>
                        <th class="py-2">Status</th>
                        <th class="py-2 text-right">Amount</th>
                        <th class="py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->creditNotes as $note)
                        <tr class="border-b">
                            <td class="py-2"><a href="{{ route('credit-notes.show', $note) }}" class="text-brand-700 hover:underline">{{ $note->number }}</a></td>
                            <td class="py-2">{{ $note->issue_date->format('d M Y') }}</td>
                            <td class="py-2 text-gray-500">{{ $note->reason }}</td>
                            <td class="py-2"><x-status-badge :status="$note->status" /></td>
                            <td class="py-2 text-right"><x-money :amount="$note->amount" /></td>
                            <td class="py-2 text-right">
                                <a href="{{ route('credit-notes.pdf', $note) }}" class="text-xs">PDF</a>
                                @if($note->status === 'applied')
                                    <form method="POST" action="{{ route('credit-notes.void', $note) }}" class="ml-2 inline" onsubmit="return confirm('Void credit note ini?')">
                                        @csrf
                                        @method('PATCH')
                                        <button class="text-xs text-error-600">Void</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div id="credit-note-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-lg bg-white p-5">
        <h3 class="font-semibold">Issue Credit Note</h3>
        <form method="POST" action="{{ route('invoices.credit-notes.store', $invoice) }}" class="mt-3 space-y-3">
            @csrf
            <x-form.input name="number" label="Number (auto if empty)" />
            <x-form.input name="issue_date" label="Date" type="date" :value="now()->toDateString()" />
            <x-form.input name="amount" label="Amount" type="number" step="0.01" />
            <x-form.input name="reason" label="Reason" />
            <x-form.input name="notes" label="Notes" />
            <div class="flex justify-end gap-2">
                <button type="button" data-modal-close class="rounded-md border border-gray-300 px-3 py-2 text-sm">Cancel</button>
                <button class="rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white">Apply</button>
            </div>
        </form>
    </div>
</div>
