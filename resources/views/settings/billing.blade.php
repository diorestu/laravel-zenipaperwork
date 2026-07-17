@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-white/90">Billing</h1>
    </div>

    <section class="grid gap-4 lg:grid-cols-3">
        @foreach ($plans as $plan)
            @php($isActive = $activePlan === $plan['slug'])
            <article @class([
                'rounded-lg border bg-white p-5 shadow-theme-xs dark:bg-white/[0.03]',
                'border-brand-500 ring-2 ring-brand-500/15 dark:border-brand-400 dark:ring-brand-400/20' => $isActive,
                'border-gray-200 dark:border-gray-800' => ! $isActive,
            ])>
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white/90">{{ $plan['name'] }}</h2>
                        <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white/90"><x-money :amount="$plan['amount']" /></p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <span class="rounded-full border border-gray-200 px-2.5 py-1 text-xs font-medium text-gray-600 dark:border-gray-700 dark:text-gray-300">Monthly</span>
                        @if ($isActive)
                            <span class="rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-700 dark:bg-brand-500/15 dark:text-brand-300">Paket Aktif</span>
                        @endif
                    </div>
                </div>
                <button type="button" @click="$dispatch('open-modal', 'confirm-payment-{{ $plan['slug'] }}')" class="mt-5 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:border-brand-500 hover:bg-brand-500 hover:text-white dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-200 dark:hover:border-brand-500 dark:hover:bg-brand-500 dark:hover:text-white">
                    Pilih Paket
                </button>
            </article>

            <x-ui.modal name="confirm-payment-{{ $plan['slug'] }}" class="max-w-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white/90">Konfirmasi Langganan Paket</h2>
                <div class="mt-4 rounded-lg border border-gray-200 p-4 dark:border-gray-800 bg-gray-50 dark:bg-white/[0.01]">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Paket Langganan:</span>
                        <span class="font-bold text-gray-900 dark:text-white/90">{{ $plan['name'] }}</span>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-sm border-t border-gray-200 dark:border-gray-800 pt-2">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Total Biaya:</span>
                        <span class="font-bold text-brand-600 dark:text-brand-400"><x-money :amount="$plan['amount']" /></span>
                    </div>
                </div>

                <form method="POST" action="{{ route('billing.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4" x-data="{ paymentMethod: 'qris' }">
                    @csrf
                    <input type="hidden" name="package" value="{{ $plan['slug'] }}">
                    <input type="hidden" name="amount" value="{{ $plan['amount'] }}">

                    <!-- Pilihan Metode Pembayaran -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Metode Pembayaran</label>
                        <div class="grid grid-cols-2 gap-3">
                            <!-- QRIS Option -->
                            <label class="flex flex-col p-3 rounded-lg border cursor-pointer transition-all duration-200"
                                :class="paymentMethod === 'qris' ? 'border-brand-500 bg-brand-50/50 dark:bg-brand-500/10' : 'border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/[0.01]'">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-gray-900 dark:text-white">QRIS (Otomatis)</span>
                                    <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="text-brand-600 focus:ring-brand-500">
                                </div>
                                <span class="text-[10px] text-gray-500">Pembayaran instan, aktif otomatis dalam 1 menit.</span>
                            </label>

                            <!-- Manual Transfer Option -->
                            <label class="flex flex-col p-3 rounded-lg border cursor-pointer transition-all duration-200"
                                :class="paymentMethod === 'manual_transfer' ? 'border-brand-500 bg-brand-50/50 dark:bg-brand-500/10' : 'border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/[0.01]'">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-gray-900 dark:text-white">Transfer Manual</span>
                                    <input type="radio" name="payment_method" value="manual_transfer" x-model="paymentMethod" class="text-brand-600 focus:ring-brand-500">
                                </div>
                                <span class="text-[10px] text-gray-500">Kirim bukti transfer, verifikasi admin maksimal 1x24 jam.</span>
                            </label>
                        </div>
                    </div>

                    <!-- Detail QRIS Info -->
                    <div x-show="paymentMethod === 'qris'" x-transition class="p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 rounded-lg text-xs leading-relaxed flex gap-2">
                        <i class="bx bx-info-circle text-base mt-0.5"></i>
                        <span>Setelah menekan tombol Konfirmasi, Anda akan diarahkan ke halaman invoice billing yang memuat kode QRIS untuk pembayaran langsung.</span>
                    </div>

                    <!-- Detail Manual Transfer Fields -->
                    <div x-show="paymentMethod === 'manual_transfer'" x-transition class="space-y-4" style="display: none;">
                        <!-- Rekening Perusahaan -->
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/20 text-amber-800 dark:text-amber-300 rounded-lg text-xs leading-relaxed">
                            <p class="font-bold mb-1">Rekening Tujuan Transfer:</p>
                            <p>Bank Central Asia (BCA)</p>
                            <p class="font-bold text-sm select-all">123-456-7890</p>
                            <p class="mt-0.5">a/n PT Paperwork Karya Bangsa</p>
                        </div>

                        <!-- Bukti Transfer -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Unggah Bukti Transfer</label>
                            <input type="file" name="proof" accept="image/*,application/pdf" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 dark:file:bg-gray-800 dark:file:text-gray-300">
                        </div>

                        <!-- Catatan -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Tambahan (Opsional)</label>
                            <textarea name="notes" rows="2" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-xs text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Misalnya nama pengirim rekening bank Anda..."></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-gray-100 dark:border-gray-800 pt-4">
                        <button type="button" @click="$dispatch('close-modal')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-750 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-850 cursor-pointer">Batal</button>
                        <button class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-theme-xs hover:bg-brand-600 cursor-pointer">Konfirmasi & Lanjutkan</button>
                    </div>
                </form>
            </x-ui.modal>
        @endforeach
    </section>

    <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white/90 mb-4">Riwayat Pembayaran</h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-left text-xs">
                <thead class="bg-gray-50 dark:bg-white/[0.02]">
                    <tr>
                        <th scope="col" class="px-4 py-3 font-semibold text-gray-500 dark:text-gray-400">Paket</th>
                        <th scope="col" class="px-4 py-3 font-semibold text-gray-500 dark:text-gray-400">Metode</th>
                        <th scope="col" class="px-4 py-3 font-semibold text-gray-500 dark:text-gray-400">Jumlah</th>
                        <th scope="col" class="px-4 py-3 font-semibold text-gray-500 dark:text-gray-400">Status</th>
                        <th scope="col" class="px-4 py-3 font-semibold text-gray-500 dark:text-gray-400">Tanggal</th>
                        <th scope="col" class="px-4 py-3 font-semibold text-gray-500 dark:text-gray-400 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($submissions as $submission)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.01]">
                            <td class="whitespace-nowrap px-4 py-2.5 font-medium text-gray-900 dark:text-white">
                                {{ str($submission->package)->headline() }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-gray-500 dark:text-gray-400 uppercase">
                                {{ str_replace('_', ' ', $submission->payment_method) }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-gray-900 dark:text-white font-medium">
                                <x-money :amount="$submission->amount" />
                            </td>
                            <td class="whitespace-nowrap px-4 py-2.5">
                                <x-status-badge :status="$submission->status" />
                            </td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-gray-500 dark:text-gray-400">
                                {{ $submission->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-2.5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('settings.billing.show', $submission) }}" 
                                       class="inline-flex items-center gap-1 rounded bg-gray-100 px-2 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200 dark:bg-gray-850 dark:text-gray-300 dark:hover:bg-gray-800 transition cursor-pointer">
                                        <i class="bx bx-show text-sm"></i>
                                        <span>Detail</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                Belum ada riwayat pembayaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
