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

    $rawTaxes = old('custom_taxes', $invoice?->normalized_custom_taxes ?: [
        ['name' => 'PPN', 'rate' => (float) ($invoice?->tax_rate ?? 11), 'type' => 'addition'],
    ]);
    if (empty($rawTaxes)) {
        $rawTaxes = [['name' => 'PPN', 'rate' => 11, 'type' => 'addition']];
    }
    $taxesJson = json_encode(array_values($rawTaxes));

    $discountType = old('discount_type', $invoice?->discount_type ?? 'fixed');
    $discountRate = (float) old('discount_rate', $invoice?->discount_rate ?? 0);
    $discountAmount = (float) old('discount_amount', $invoice?->discount_amount ?? 0);

    $defaultNotes = '';
    if (!$invoice) {
        $defaultNotes = "Terima kasih atas kepercayaan Anda.\n\n";
        if ($bankAccounts->isNotEmpty()) {
            $defaultNotes .= "Pembayaran dapat ditransfer melalui rekening berikut:\n";
            foreach ($bankAccounts as $acc) {
                $defaultNotes .= "- {$acc->bank_name} a/n {$acc->account_name} ({$acc->account_number})\n";
            }
        }
    } else {
        $defaultNotes = $invoice->notes;
    }
@endphp
<form method="POST" action="{{ $action }}" @submit="validateForm($event)" class="space-y-5 rounded-lg border border-gray-200 bg-white p-5" x-data="itemForm({ productData: {{ $productJson }}, existingItems: {{ $itemsJson }}, existingTerms: {{ $termsJson }}, existingTaxes: {{ $taxesJson }}, discountType: '{{ $discountType }}', discountRate: {{ $discountRate }}, discountAmount: {{ $discountAmount }} })">
    @csrf
    @method($method)
    <div class="grid gap-4 sm:grid-cols-2">
        <!-- Searchable Client Select -->
        <div x-data="{
            openClient: false,
            clientSearch: '',
            selectedClientId: @js((string) old('client_id', $invoice?->client_id ?? '')),
            selectedClientName: '',
            clientsList: @js($clients->map(fn($c) => ['id' => (string) $c->id, 'name' => $c->name, 'company' => $c->company_name ?? ''])->values()),
            init() {
                const found = this.clientsList.find(c => c.id === this.selectedClientId);
                if (found) { this.selectedClientName = found.name + (found.company ? ' (' + found.company + ')' : ''); }
            },
            get filteredClients() {
                if (!this.clientSearch) return this.clientsList;
                return this.clientsList.filter(c => c.name.toLowerCase().includes(this.clientSearch.toLowerCase()) || c.company.toLowerCase().includes(this.clientSearch.toLowerCase()));
            },
            selectClient(c) {
                this.selectedClientId = c.id;
                this.selectedClientName = c.name + (c.company ? ' (' + c.company + ')' : '');
                this.openClient = false;
                this.clientSearch = '';
            }
        }" class="relative">
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Klien <span class="text-error-500">*</span></label>
            <input type="hidden" name="client_id" :value="selectedClientId" required>

            <div @click="openClient = !openClient" @click.away="openClient = false" class="relative cursor-pointer">
                <div class="w-full rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 text-sm text-gray-900 focus:border-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white flex items-center justify-between shadow-sm">
                    <span x-text="selectedClientName || '— Cari & Pilih Klien —'" :class="!selectedClientName ? 'text-gray-400' : ''"></span>
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>

                <div x-show="openClient" x-transition class="absolute z-50 mt-1 w-full rounded-md border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900 max-h-60 overflow-auto p-2 space-y-1">
                    <input type="text" x-model="clientSearch" @click.stop placeholder="Ketik nama / perusahaan klien..." class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white mb-2">

                    <template x-for="c in filteredClients" :key="c.id">
                        <div @click="selectClient(c)" class="cursor-pointer rounded px-3 py-2 text-xs hover:bg-brand-50 hover:text-brand-700 dark:hover:bg-brand-500/20 dark:hover:text-brand-300 flex items-center justify-between" :class="selectedClientId === c.id ? 'bg-brand-50 text-brand-700 font-bold dark:bg-brand-500/20 dark:text-brand-300' : 'text-gray-700 dark:text-gray-300'">
                            <span x-text="c.name + (c.company ? ' (' + c.company + ')' : '')"></span>
                            <span x-show="selectedClientId === c.id" class="text-brand-600 dark:text-brand-400">✓</span>
                        </div>
                    </template>
                    <div x-show="filteredClients.length === 0" class="px-3 py-2 text-xs text-gray-400 text-center">
                        Klien tidak ditemukan
                    </div>
                </div>
            </div>
            @error('client_id')<span class="block text-xs text-error-600 mt-1">{{ $message }}</span>@enderror
        </div>

        @if ($invoice)
            <x-form.input name="number" label="Nomor Invoice" :value="$invoice->number" />
        @else
            <div>
                <x-form.input name="number" label="Nomor Invoice (Otomatis jika dikosongkan)" :value="old('number')" placeholder="Contoh: INV/2026/08/0001" />
                <p class="mt-1 text-xs text-gray-500">Jika dikosongkan, sistem akan menggenerate nomor invoice secara otomatis.</p>
            </div>
        @endif

        <x-form.input name="issue_date" label="Tanggal Terbit" type="date" :value="optional($invoice?->issue_date)->format('Y-m-d') ?? now()->toDateString()" />
        <x-form.input name="due_date" label="Jatuh Tempo" type="date" :value="optional($invoice?->due_date)->format('Y-m-d') ?? now()->addDays(7)->toDateString()" />
        <input type="hidden" name="tax_rate" :value="taxRate">
        <input type="hidden" name="pph_rate" :value="pphRate">
        <input type="hidden" name="pph_amount" :value="totalDeductions">

        @if ($bankAccounts->isNotEmpty())
            <x-form.select name="bank_account_id" label="Rekening Pembayaran Utama">
                <option value="">— Semua Rekening (Daftarkan Semua di Catatan) —</option>
                @foreach ($bankAccounts as $account)
                    <option value="{{ $account->id }}" @selected((int) old('bank_account_id', $invoice?->bank_account_id) === $account->id)>
                        {{ $account->bank_name }} - {{ $account->account_number }} (a/n {{ $account->account_name }})
                    </option>
                @endforeach
            </x-form.select>
        @endif
    </div>

    <!-- Tipe Invoice & Referensi DP -->
    <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4 dark:border-gray-800 dark:bg-white/[0.02]"
         x-data="{
             invoiceType: @js(old('invoice_type', $invoice?->invoice_type ?? request('invoice_type', 'standard'))),
             parentId: @js((string) old('parent_invoice_id', $invoice?->parent_invoice_id ?? request('parent_invoice_id', '')))
         }">
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">Tipe Invoice</label>
        <div class="grid gap-3 sm:grid-cols-3 mb-3">
            <label class="flex items-center gap-2.5 rounded-lg border p-3 cursor-pointer transition"
                   :class="invoiceType === 'standard' ? 'border-brand-500 bg-brand-50/50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-300 font-semibold' : 'border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 text-gray-700 dark:text-gray-300'">
                <input type="radio" name="invoice_type" value="standard" x-model="invoiceType" class="text-brand-600 focus:ring-brand-600">
                <div>
                    <span class="block text-xs font-semibold">Standard</span>
                    <span class="block text-[11px] text-gray-500 dark:text-gray-400">Tagihan Biasa</span>
                </div>
            </label>

            <label class="flex items-center gap-2.5 rounded-lg border p-3 cursor-pointer transition"
                   :class="invoiceType === 'down_payment' ? 'border-amber-500 bg-amber-50/50 dark:bg-amber-500/10 text-amber-800 dark:text-amber-300 font-semibold' : 'border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 text-gray-700 dark:text-gray-300'">
                <input type="radio" name="invoice_type" value="down_payment" x-model="invoiceType" class="text-amber-600 focus:ring-amber-600">
                <div>
                    <span class="block text-xs font-semibold">Uang Muka (DP)</span>
                    <span class="block text-[11px] text-gray-500 dark:text-gray-400">Invoice Uang Muka</span>
                </div>
            </label>

            <label class="flex items-center gap-2.5 rounded-lg border p-3 cursor-pointer transition"
                   :class="invoiceType === 'settlement' ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-500/10 text-indigo-800 dark:text-indigo-300 font-semibold' : 'border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 text-gray-700 dark:text-gray-300'">
                <input type="radio" name="invoice_type" value="settlement" x-model="invoiceType" class="text-indigo-600 focus:ring-indigo-600">
                <div>
                    <span class="block text-xs font-semibold">Pelunasan</span>
                    <span class="block text-[11px] text-gray-500 dark:text-gray-400">Pelunasan dari DP</span>
                </div>
            </label>
        </div>

        <div x-show="invoiceType === 'settlement'" x-collapse class="mt-3">
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Pilih Invoice DP Referensi <span class="text-error-500">*</span></label>
            <select name="parent_invoice_id" x-model="parentId" class="w-full rounded-md border border-gray-300 bg-white py-2 px-3 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                <option value="">— Pilih Invoice DP Terkait —</option>
                @foreach ($parentInvoices ?? [] as $pInv)
                    @if (!$invoice || $pInv->id !== $invoice->id)
                        <option value="{{ $pInv->id }}" @selected((int) old('parent_invoice_id', $invoice?->parent_invoice_id ?? request('parent_invoice_id')) === $pInv->id)>
                            {{ $pInv->number }} — {{ $pInv->client?->name }} (Rp {{ number_format((float) $pInv->total, 0, ',', '.') }} - {{ strtoupper($pInv->status) }})
                        </option>
                    @endif
                @endforeach
            </select>
        </div>
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
                <div class="grid gap-3 rounded-md border border-gray-200 p-3 sm:grid-cols-[1fr_7rem_9rem_2rem]" x-data="{ openProd: false, prodSearch: '' }">
                    <!-- Searchable Product Dropdown -->
                    <div class="relative" @click.away="openProd = false">
                        <div @click="openProd = !openProd" class="w-full cursor-pointer rounded-md border border-gray-300 bg-white py-2 pl-3 pr-8 text-sm text-gray-900 focus:border-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white flex items-center justify-between shadow-sm">
                            <span x-text="getProductName(item.product_id) || item.description || '— Cari / Pilih Produk Master —'" :class="!getProductName(item.product_id) && !item.description ? 'text-gray-400' : ''" class="truncate"></span>
                            <svg class="h-4 w-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>

                        <div x-show="openProd" x-transition class="absolute z-50 mt-1 w-full rounded-md border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900 max-h-56 overflow-auto p-2 space-y-1">
                            <input type="text" x-model="prodSearch" @click.stop placeholder="Ketik nama produk..." class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs text-gray-900 focus:border-brand-500 focus:bg-white focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white mb-1">

                            <div @click="item.product_id = ''; openProd = false" class="cursor-pointer rounded px-2.5 py-1.5 text-xs text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 border-b border-gray-100 dark:border-gray-800 mb-1">
                                <em>— Item Manual (Input Deskripsi Bebas) —</em>
                            </div>

                            <template x-for="p in filterProducts(prodSearch)" :key="p.id">
                                <div @click="selectProductItem(index, p); openProd = false; prodSearch = ''" class="cursor-pointer rounded px-2.5 py-1.5 text-xs hover:bg-brand-50 hover:text-brand-700 dark:hover:bg-brand-500/20 dark:hover:text-brand-300 flex items-center justify-between" :class="item.product_id == p.id ? 'bg-brand-50 text-brand-700 font-bold dark:bg-brand-500/20 dark:text-brand-300' : 'text-gray-700 dark:text-gray-300'">
                                    <span x-text="p.name"></span>
                                    <span class="font-mono text-[11px] text-gray-500 dark:text-gray-400" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(p.price)"></span>
                                </div>
                            </template>
                        </div>
                        <div class="mt-2" x-show="!item.product_id">
                            <input type="text" x-model="item.description" class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs text-gray-800 focus:border-brand-500 focus:bg-white focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white" placeholder="Ketik uraian / deskripsi item manual...">
                        </div>
                    </div>

                    <input x-on:focus="$el.value = item.quantity; $el.select()" x-on:blur="item.quantity = fixNum($el.value); $el.value = fmt(item.quantity)" x-on:input="item.quantity = fixNum($el.value)" class="rounded-md border border-gray-300 px-3 py-2 text-sm text-right h-10" x-bind:value="fmt(item.quantity)" placeholder="Jumlah">
                    <input x-on:focus="if(!$el.readOnly){ $el.value = item.unit_price; $el.select() }" x-on:blur="if(!$el.readOnly){ item.unit_price = fixNum($el.value); $el.value = fmt(item.unit_price) }" x-bind:readonly="!!item.product_id" x-bind:class="!!item.product_id ? 'rounded-md border border-gray-300 px-3 py-2 text-sm text-right bg-gray-50 text-gray-600 h-10' : 'rounded-md border border-gray-300 px-3 py-2 text-sm text-right bg-white h-10'" x-bind:value="fmt(item.unit_price)" placeholder="Harga">
                    <button type="button" x-on:click="removeRow(index)" x-show="items.length > 1" class="flex items-center justify-center text-gray-400 hover:text-error-600" title="Hapus">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <!-- Discount Section -->
    <div class="space-y-3 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-white/[0.02]">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white/90">Diskon (Opsional)</h2>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Berikan potongan harga pada subtotal sebelum kalkulasi pajak.</p>
            </div>
        </div>
        <div class="grid gap-3 sm:grid-cols-[12rem_1fr]">
            <select x-model="discountType" name="discount_type" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-xs text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option value="fixed">Nominal (Rp)</option>
                <option value="percentage">Persentase (%)</option>
            </select>

            <template x-if="discountType === 'percentage'">
                <div class="relative flex items-center">
                    <input type="number" step="0.01" min="0" max="100" name="discount_rate" x-model="discountRate" class="w-full rounded-md border border-gray-300 bg-white pl-3 pr-8 py-2 text-sm text-right text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="0">
                    <span class="absolute right-3 text-xs font-bold text-gray-400">%</span>
                    <input type="hidden" name="discount_amount" :value="calculatedDiscountAmount">
                </div>
            </template>

            <template x-if="discountType !== 'percentage'">
                <div>
                    <input type="hidden" name="discount_rate" value="0">
                    <input x-on:focus="$el.value = discountAmount; $el.select()" x-on:blur="discountAmount = fixMoney($el.value); $el.value = fmt(discountAmount)" x-on:input="$el.value = moneyDigits($el.value, 12); discountAmount = fixMoney($el.value)" x-bind:value="fmt(discountAmount)" inputmode="numeric" maxlength="12" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-right text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Rp 0">
                    <input type="hidden" name="discount_amount" :value="discountAmount">
                </div>
            </template>
        </div>
    </div>

    <!-- Custom Taxes Section -->
    <div class="space-y-3 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-white/[0.02]">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white/90">Pajak & Potongan (Custom Tax)</h2>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Atur pajak penambahan (seperti PPN) atau potongan pajak (seperti PPh 23).</p>
            </div>
            <button type="button" x-on:click="addTax()" class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-white/[0.03]">+ Tambah Pajak / Potongan</button>
        </div>

        <template x-for="(tax, index) in customTaxes" :key="index">
            <div>
                <input type="hidden" x-bind:name="'custom_taxes[' + index + '][name]'" x-model="tax.name">
                <input type="hidden" x-bind:name="'custom_taxes[' + index + '][rate]'" x-model="tax.rate">
                <input type="hidden" x-bind:name="'custom_taxes[' + index + '][type]'" x-model="tax.type">
                <div class="grid gap-3 rounded-md border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-900 sm:grid-cols-[1fr_7rem_12rem_2rem]">
                    <input x-model="tax.name" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="Nama pajak (misal PPN, PPh 23)">
                    <div class="relative flex items-center">
                        <input type="number" step="0.01" min="0" max="100" x-model="tax.rate" class="w-full rounded-md border border-gray-300 bg-white pl-3 pr-7 py-2 text-sm text-right text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="0">
                        <span class="absolute right-2.5 text-xs font-bold text-gray-400">%</span>
                    </div>
                    <select x-model="tax.type" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-xs text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="addition">+ Penambahan Nilai (seperti PPN)</option>
                        <option value="deduction">- Pengurangan Nilai (seperti PPh)</option>
                    </select>
                    <button type="button" x-on:click="removeTax(index)" class="flex items-center justify-center text-gray-400 hover:text-error-600" title="Hapus pajak">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </template>

        @error('custom_taxes')<span class="block text-xs text-error-600">{{ $message }}</span>@enderror
    </div>

    <!-- Split Payment Section -->
    <div class="space-y-3 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-white/[0.02]">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white/90">Pembayaran Bertahap (Split Payment)</h2>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Bagi total tagihan menjadi beberapa termin pembayaran dengan jatuh tempo berbeda.</p>
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

    Alpine.data('itemForm', ({ productData, existingItems, existingTerms, existingTaxes, discountType, discountRate, discountAmount }) => ({
        productData,
        items: (existingItems && existingItems.length > 0)
            ? existingItems
            : [{ product_id: '', description: '', quantity: 1, unit_price: 0 }],
        paymentTerms: (existingTerms && existingTerms.length > 0)
            ? existingTerms
            : [],
        customTaxes: (existingTaxes && existingTaxes.length > 0)
            ? existingTaxes
            : [{ name: 'PPN', rate: 11, type: 'addition' }],
        discountType: discountType || 'fixed',
        discountRate: discountRate || 0,
        discountAmount: discountAmount || 0,

        filterProducts(search) {
            if (!search) return this.productData;
            return this.productData.filter(p => p.name.toLowerCase().includes(search.toLowerCase()));
        },

        getProductName(id) {
            if (!id) return '';
            const p = this.productData.find(prod => String(prod.id) === String(id));
            return p ? p.name + ' (Rp ' + fmt(p.price) + ')' : '';
        },

        selectProductItem(index, p) {
            this.items[index].product_id = String(p.id);
            this.items[index].description = p.description || p.name;
            this.items[index].unit_price = p.price;
        },

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

        addTax() {
            this.customTaxes.push({ name: 'Pajak', rate: 0, type: 'addition' });
        },

        removeTax(index) {
            this.customTaxes.splice(index, 1);
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

        get calculatedDiscountAmount() {
            const subtotal = this.subtotal;
            if (this.discountType === 'percentage') {
                const rate = parseFloat(this.discountRate) || 0;
                return Math.min(Math.round((subtotal * (rate / 100)) * 100) / 100, subtotal);
            }
            return Math.min(parseFloat(this.discountAmount) || 0, subtotal);
        },

        get discountedSubtotal() {
            return Math.max(this.subtotal - this.calculatedDiscountAmount, 0);
        },

        get calculatedTaxes() {
            const discountedSubtotal = this.discountedSubtotal;
            return this.customTaxes.map(tax => {
                const name = tax.name || 'Pajak';
                const rate = parseFloat(tax.rate) || 0;
                const type = tax.type === 'deduction' ? 'deduction' : 'addition';
                const amount = Math.round((discountedSubtotal * (rate / 100)) * 100) / 100;
                return { name, rate, type, amount };
            });
        },

        get taxRate() {
            const additionTaxes = this.customTaxes.filter(t => t.type !== 'deduction');
            return additionTaxes.reduce((sum, t) => sum + (parseFloat(t.rate) || 0), 0);
        },

        get pphRate() {
            const deductionTaxes = this.customTaxes.filter(t => t.type === 'deduction');
            return deductionTaxes.reduce((sum, t) => sum + (parseFloat(t.rate) || 0), 0);
        },

        get totalAdditions() {
            return this.calculatedTaxes
                .filter(t => t.type === 'addition')
                .reduce((sum, t) => sum + t.amount, 0);
        },

        get totalDeductions() {
            return this.calculatedTaxes
                .filter(t => t.type === 'deduction')
                .reduce((sum, t) => sum + t.amount, 0);
        },

        get invoiceTotal() {
            const discountedSubtotal = this.discountedSubtotal;
            return Math.max(discountedSubtotal + this.totalAdditions - this.totalDeductions, 0);
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
