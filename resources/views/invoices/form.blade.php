@php
    $rawItems = old('items', $invoice?->items?->toArray() ?: [['product_id' => '', 'description' => '', 'quantity' => 1, 'unit_price' => 0]]);
    $productJson = $products->where('is_active', true)->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'description' => $p->description ?? $p->name, 'price' => (float) $p->price, 'unit' => $p->unit])->values()->toJson();
    $itemsJson = json_encode(array_map(fn($i) => [
        'product_id' => (string) ($i['product_id'] ?? ''),
        'description' => $i['description'] ?? '',
        'quantity' => (float) ($i['quantity'] ?? 1),
        'unit_price' => (float) ($i['unit_price'] ?? 0),
    ], $rawItems));
    $rawTerms = old('payment_terms', $invoice?->paymentTerms?->toArray() ?: []);
    $termsJson = json_encode(array_values(array_map(fn($term, $index) => [
        'label' => $term['label'] ?? 'Termin '.($index + 1),
        'amount' => (float) ($term['amount'] ?? 0),
        'due_date' => $term['due_date'] ?? '',
    ], $rawTerms, array_keys($rawTerms))));

    $defaultNotes = '';
    if (!$invoice) {
        $defaultNotes = "Terima kasih atas kerja sama Anda.\n\n";
        if ($bankAccounts->isNotEmpty()) {
            $defaultNotes .= "Pembayaran dapat ditransfer melalui rekening berikut:\n";
            foreach ($bankAccounts as $acc) {
                $defaultNotes .= "- {$acc->bank_name} a/n {$acc->account_name} ({$acc->account_number})\n";
            }
            $defaultNotes .= "\n";
        }
        $defaultNotes .= "Konfirmasi pembayaran dapat dilakukan dalam jangka waktu maksimal 7 hari setelah tanggal invoice ini.";
    } else {
        $defaultNotes = $invoice->notes;
    }
@endphp
<form method="POST" action="{{ $action }}" @submit="validateForm($event)" class="space-y-5 rounded-lg border border-gray-200 bg-white p-5" x-data="itemForm({ productData: {{ $productJson }}, existingItems: {{ $itemsJson }}, existingTerms: {{ $termsJson }} })">
    @csrf
    @method($method)
    <div class="grid gap-4 sm:grid-cols-2">
        <x-form.select name="client_id" label="Klien" :value="$invoice?->client_id">
            @foreach ($clients as $client)
                <option value="{{ $client->id }}" @selected((int) old('client_id', $invoice?->client_id) === $client->id)>{{ $client->name }}</option>
            @endforeach
        </x-form.select>
        <x-form.input name="number" label="Nomor" :value="$invoice?->number ?? 'INV-'.now()->format('Ymd-His')" />
        <x-form.input name="issue_date" label="Tanggal Terbit" type="date" :value="optional($invoice?->issue_date)->format('Y-m-d') ?? now()->toDateString()" />
        <x-form.input name="due_date" label="Jatuh Tempo" type="date" :value="optional($invoice?->due_date)->format('Y-m-d')" />
        <x-form.input name="tax_rate" label="PPN (%)" type="number" step="0.01" :value="$invoice?->tax_rate ?? 0" />
        <x-form.input name="pph_rate" label="PPh (%)" type="number" step="0.01" :value="$invoice?->pph_rate ?? 0" />
        
        @if($bankAccounts->count() > 1)
            <x-form.select name="bank_account_id" label="Rekening Bank (Pembayaran)">
                <option value="">— Gunakan Semua Rekening —</option>
                @foreach ($bankAccounts as $acc)
                    <option value="{{ $acc->id }}" @selected((int) old('bank_account_id', $invoice?->bank_account_id) === $acc->id)>
                        {{ $acc->bank_name }} - {{ $acc->account_number }} ({{ $acc->account_name }})
                    </option>
                @endforeach
            </x-form.select>
        @elseif($bankAccounts->count() === 1)
            <input type="hidden" name="bank_account_id" value="{{ $bankAccounts->first()->id }}">
        @endif
        
        <input type="hidden" name="down_payment_amount" :value="paymentTerms.length ? paymentTerms[0].amount : 0">
    </div>

    <!-- Recurring Invoice Settings -->
    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-white/[0.02]" x-data="{ isRecurring: {{ old('is_recurring', $invoice?->is_recurring ? 'true' : 'false') }} }">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="hidden" name="is_recurring" value="0">
            <input type="checkbox" name="is_recurring" value="1" x-model="isRecurring" class="rounded border-gray-300 text-brand-600 focus:ring-brand-600">
            <span class="text-sm font-medium text-gray-900 dark:text-white">Jadikan Invoice Berulang (Recurring)</span>
        </label>
        
        <div x-show="isRecurring" x-collapse class="mt-3">
            <x-form.select name="recurring_cycle" label="Siklus Perulangan">
                <option value="monthly" @selected(old('recurring_cycle', $invoice?->recurring_cycle) === 'monthly')>Bulanan</option>
                <option value="yearly" @selected(old('recurring_cycle', $invoice?->recurring_cycle) === 'yearly')>Tahunan</option>
            </x-form.select>
            <p class="mt-1 text-xs text-gray-500">Invoice baru akan otomatis dibuat sesuai siklus yang dipilih berdasarkan Tanggal Terbit.</p>
        </div>
    </div>

    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold">Item</h2>
            <button type="button" x-on:click="addRow()" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium">+ Tambah Baris</button>
        </div>

        <template x-for="(item, index) in items" :key="index">
            <div>
                <input type="hidden" x-bind:name="'items[' + index + '][product_id]'" x-model="item.product_id">
                <input type="hidden" x-bind:name="'items[' + index + '][description]'" x-model="item.description">
                <input type="hidden" x-bind:name="'items[' + index + '][quantity]'" x-model="item.quantity">
                <input type="hidden" x-bind:name="'items[' + index + '][unit_price]'" x-model="item.unit_price">
                <div class="grid gap-3 rounded-md border border-gray-200 p-3 sm:grid-cols-[1fr_7rem_9rem_2rem]">
                    <select x-model="item.product_id" x-on:change="onSelect(index)" class="w-full appearance-none rounded-md border border-gray-300 py-2 pl-3 pr-10 text-sm focus:border-gray-900 focus:outline-none bg-white bg-[url('data:image/svg+xml,%3Csvg%20xmlns=%27http://www.w3.org/2000/svg%27%20fill=%27none%27%20viewBox=%270%200%2024%2024%27%20stroke-width=%271.5%27%20stroke=%27%239ca3af%27%3E%3Cpath%20stroke-linecap=%27round%27%20stroke-linejoin=%27round%27%20d=%27m19.5%208.25-7.5%207.5-7.5-7.5%27%20/%3E%3C/svg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_0.75rem_center] bg-no-repeat">
                        <option value="">— Pilih Produk —</option>
                        <template x-for="p in productData" :key="p.id">
                            <option :value="p.id" x-text="p.name + ' (Rp ' + new Intl.NumberFormat('id-ID').format(p.price) + ')'"></option>
                        </template>
                    </select>
                    <input x-on:focus="$el.value = item.quantity; $el.select()" x-on:blur="item.quantity = fixNum($el.value); $el.value = fmt(item.quantity)" x-on:input="item.quantity = fixNum($el.value)" class="rounded-md border border-gray-300 px-3 py-2 text-sm text-right" x-bind:value="fmt(item.quantity)" placeholder="Jumlah">
                    <input x-on:focus="if(!$el.readOnly){ $el.value = item.unit_price; $el.select() }" x-on:blur="if(!$el.readOnly){ item.unit_price = fixNum($el.value); $el.value = fmt(item.unit_price) }" x-bind:readonly="!!item.product_id" x-bind:class="!!item.product_id ? 'rounded-md border border-gray-300 px-3 py-2 text-sm text-right bg-gray-50 text-gray-600' : 'rounded-md border border-gray-300 px-3 py-2 text-sm text-right bg-white'" x-bind:value="fmt(item.unit_price)" placeholder="Harga">
                    <button type="button" x-on:click="removeRow(index)" x-show="items.length > 1" class="flex items-center justify-center text-gray-400 hover:text-error-600" title="Hapus">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <div class="space-y-3 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-white/[0.02]">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white/90">Pembayaran Bertahap</h2>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Tentukan termin dan nominal yang harus dibayar saat pencatatan pembayaran.</p>
            </div>
            <button type="button" x-on:click="addTerm()" class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-white/[0.03]">+ Tambah Termin</button>
        </div>

        <template x-for="(term, index) in paymentTerms" :key="index">
            <div>
                <input type="hidden" x-bind:name="'payment_terms[' + index + '][label]'" x-model="term.label">
                <input type="hidden" x-bind:name="'payment_terms[' + index + '][amount]'" x-model="term.amount">
                <input type="hidden" x-bind:name="'payment_terms[' + index + '][due_date]'" x-model="term.due_date">
                <div class="grid gap-3 rounded-md border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-900 sm:grid-cols-[1fr_10rem_10rem_2rem]">
                    <input x-model="term.label" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="Nama termin">
                    <input x-on:focus="$el.value = term.amount; $el.select()" x-on:blur="clampTermAmount(index, $el); $el.value = fmt(term.amount)" x-on:input="$el.value = moneyDigits($el.value, 12); clampTermAmount(index, $el)" x-bind:value="fmt(term.amount)" inputmode="numeric" maxlength="12" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-right text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="Nominal">
                    <input type="date" x-model="term.due_date" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <button type="button" x-on:click="removeTerm(index)" class="flex items-center justify-center text-gray-400 hover:text-error-600" title="Hapus termin">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </template>

        @error('payment_terms')<span class="block text-xs text-error-600">{{ $message }}</span>@enderror
    </div>

    <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan / Footer Dokumen</label>
        <textarea name="notes" rows="4" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-500" placeholder="Tambahkan catatan khusus, informasi bank, atau ucapan terima kasih...">{{ old('notes', $defaultNotes) }}</textarea>
    </div>

    <button class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white cursor-pointer">Simpan Invoice</button>
</form>

<script>
document.addEventListener('alpine:init', () => {
    const fmt = (n) => new Intl.NumberFormat('id-ID').format(n || 0);

    Alpine.data('itemForm', ({ productData, existingItems, existingTerms }) => ({
        productData,
        items: (existingItems && existingItems.length > 0)
            ? existingItems
            : [{ product_id: '', description: '', quantity: 1, unit_price: 0 }],
        paymentTerms: (existingTerms && existingTerms.length > 0)
            ? existingTerms
            : [],

        onSelect(index) {
            const pid = this.items[index].product_id;
            const p = this.productData.find(p => String(p.id) === String(pid));
            if (p) {
                this.items[index].description = p.description;
                this.items[index].unit_price = p.price;
            } else {
                this.items[index].description = '';
                this.items[index].unit_price = 0;
            }
        },

        addRow() {
            this.items.push({ product_id: '', description: '', quantity: 1, unit_price: 0 });
        },

        removeRow(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },

        addTerm() {
            this.paymentTerms.push({
                label: 'Termin ' + (this.paymentTerms.length + 1),
                amount: 0,
                due_date: '',
            });
        },

        removeTerm(index) {
            this.paymentTerms.splice(index, 1);
        },

        get subtotal() {
            return this.items.reduce((total, item) => total + (Number(item.quantity) * Number(item.unit_price)), 0);
        },

        get taxRate() {
            return this.fixNum(document.querySelector('[name="tax_rate"]')?.value ?? 0);
        },

        get pphRate() {
            return this.fixNum(document.querySelector('[name="pph_rate"]')?.value ?? 0);
        },

        get invoiceTotal() {
            const subtotal = this.subtotal;
            const taxTotal = subtotal * (this.taxRate / 100);
            const pphTotal = subtotal * (this.pphRate / 100);

            return Math.max(subtotal + taxTotal - pphTotal, 0);
        },

        termsTotalExcept(index) {
            return this.paymentTerms.reduce((total, term, termIndex) => {
                return termIndex === index ? total : total + Number(term.amount || 0);
            }, 0);
        },

        maxTermAmount(index) {
            return Math.max(this.invoiceTotal - this.termsTotalExcept(index), 0);
        },

        clampTermAmount(index, input = null) {
            const enteredAmount = this.fixMoney(input?.value ?? this.paymentTerms[index]?.amount ?? 0, 12);
            const maxAmount = this.maxTermAmount(index);
            const nextAmount = Math.min(enteredAmount, maxAmount);

            this.paymentTerms[index].amount = nextAmount;

            if (input && enteredAmount > maxAmount) {
                input.value = String(Math.trunc(nextAmount));
            }
        },

        fixNum(raw) {
            const cleaned = String(raw).replace(/[^\d,.-]/g, '').replace('.', '').replace(',', '.');
            const n = parseFloat(cleaned);
            return isNaN(n) || n < 0 ? 0 : n;
        },

        moneyDigits(raw, maxDigits = 12) {
            return String(raw).replace(/\D/g, '').slice(0, maxDigits);
        },

        fixMoney(raw, maxDigits = 12) {
            const digits = this.moneyDigits(raw, maxDigits);
            const n = Number(digits);

            return Number.isFinite(n) && n > 0 ? n : 0;
        },

        validateForm(e) {
            const clientId = document.querySelector('[name="client_id"]')?.value;
            if (!clientId) {
                alert('Silakan pilih Klien terlebih dahulu.');
                e.preventDefault();
                return false;
            }

            if (!this.items || this.items.length === 0) {
                alert('Invoice harus memiliki setidaknya 1 item produk/jasa.');
                e.preventDefault();
                return false;
            }

            for (let i = 0; i < this.items.length; i++) {
                const item = this.items[i];
                if (!item.product_id && !item.description) {
                    alert(`Item ke-${i + 1}: Silakan pilih produk atau isi deskripsi.`);
                    e.preventDefault();
                    return false;
                }
                if (Number(item.quantity) <= 0) {
                    alert(`Item ke-${i + 1}: Jumlah (Qty) harus lebih besar dari 0.`);
                    e.preventDefault();
                    return false;
                }
                if (Number(item.unit_price) < 0) {
                    alert(`Item ke-${i + 1}: Harga satuan tidak boleh bernilai negatif.`);
                    e.preventDefault();
                    return false;
                }
            }

            if (this.paymentTerms && this.paymentTerms.length > 0) {
                for (let i = 0; i < this.paymentTerms.length; i++) {
                    const term = this.paymentTerms[i];
                    if (Number(term.amount) <= 0) {
                        alert(`Termin ke-${i + 1}: Nominal termin harus lebih besar dari Rp 0.`);
                        e.preventDefault();
                        return false;
                    }
                }
            }
        },

        fmt,
    }));
});
</script>
