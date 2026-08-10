@extends('layouts.app')

@section('content')
<div class="space-y-5">
    @include('settings.partials.settings-nav')

    <div class="flex items-center justify-between">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-white/90">Rekening Bank</h1>
        <button type="button" @click="$dispatch('open-modal', 'create-bank-account')" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">Tambah Rekening Bank</button>
    </div>

    <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
        <form method="GET" action="{{ route('settings.bank-accounts') }}" class="grid gap-3 sm:grid-cols-5">
            <input name="search" value="{{ request('search') }}" placeholder="Cari rekening" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-500">
            <select name="bank" class="appearance-none rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 text-sm text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-500 bg-[url('data:image/svg+xml,%3Csvg%20xmlns=%27http://www.w3.org/2000/svg%27%20fill=%27none%27%20viewBox=%270%200%2024%2024%27%20stroke-width=%271.5%27%20stroke=%27%239ca3af%27%3E%3Cpath%20stroke-linecap=%27round%27%20stroke-linejoin=%27round%27%20d=%27m19.5%208.25-7.5%207.5-7.5-7.5%27%20/%3E%3C/svg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_0.75rem_center] bg-no-repeat">
                <option value="">Semua Bank</option>
                @foreach ($banks as $bank)
                    <option value="{{ $bank }}" @selected(request('bank') === $bank)>{{ $bank }}</option>
                @endforeach
            </select>
            <select name="currency" class="appearance-none rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 text-sm text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-500 bg-[url('data:image/svg+xml,%3Csvg%20xmlns=%27http://www.w3.org/2000/svg%27%20fill=%27none%27%20viewBox=%270%200%2024%2024%27%20stroke-width=%271.5%27%20stroke=%27%239ca3af%27%3E%3Cpath%20stroke-linecap=%27round%27%20stroke-linejoin=%27round%27%20d=%27m19.5%208.25-7.5%207.5-7.5-7.5%27%20/%3E%3C/svg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_0.75rem_center] bg-no-repeat">
                <option value="">Semua Mata Uang</option>
                @foreach ($currencies as $currency)
                    <option value="{{ $currency }}" @selected(request('currency') === $currency)>{{ $currency }}</option>
                @endforeach
            </select>
            <select name="status" class="appearance-none rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 text-sm text-gray-800 focus:border-gray-900 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-500 bg-[url('data:image/svg+xml,%3Csvg%20xmlns=%27http://www.w3.org/2000/svg%27%20fill=%27none%27%20viewBox=%270%200%2024%2024%27%20stroke-width=%271.5%27%20stroke=%27%239ca3af%27%3E%3Cpath%20stroke-linecap=%27round%27%20stroke-linejoin=%27round%27%20d=%27m19.5%208.25-7.5%207.5-7.5-7.5%27%20/%3E%3C/svg%3E')] bg-[length:1.25rem_1.25rem] bg-[right_0.75rem_center] bg-no-repeat">
                <option value="">Semua Status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
            </select>
            <button class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 dark:bg-brand-600 dark:hover:bg-brand-500">Filter</button>
        </form>

        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="px-4 py-3">Bank</th>
                        <th class="px-4 py-3">Nama Rekening</th>
                        <th class="px-4 py-3">Nomor Rekening</th>
                        <th class="px-4 py-3">Cabang</th>
                        <th class="px-4 py-3">Mata Uang</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($bankAccounts as $account)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white/90">{{ $account->bank_name }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $account->account_name }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300 font-mono">{{ $account->account_number }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $account->branch ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $account->currency }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$account->is_active ? 'active' : 'inactive'" /></td>
                            <td class="px-4 py-3 text-right">
                                <button type="button" @click="$dispatch('open-modal', 'edit-bank-account-{{ $account->id }}')" class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">Edit</button>
                            </td>
                        </tr>
                        <x-ui.modal name="edit-bank-account-{{ $account->id }}" :is-open="(int) request('edit') === $account->id" class="max-w-xl p-6">
                            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white/90">Edit Rekening Bank</h2>
                            @include('settings.partials.bank-account-form', ['account' => $account, 'action' => route('settings.bank-accounts.update', $account), 'method' => 'PUT'])
                        </x-ui.modal>
                    @empty
                        <tr>
                            <td colspan="7"><x-table.empty>Belum ada rekening bank.</x-table.empty></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $bankAccounts->links() }}</div>
    </section>

    <x-ui.modal name="create-bank-account" :is-open="request('modal') === 'create'" class="max-w-xl p-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white/90">Tambah Rekening Bank</h2>
        @include('settings.partials.bank-account-form', ['account' => null, 'action' => route('settings.bank-accounts.store'), 'method' => 'POST'])
    </x-ui.modal>
</div>
@endsection
