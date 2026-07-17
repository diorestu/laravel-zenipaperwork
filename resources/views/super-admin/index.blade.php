@extends('layouts.app')

@section('content')
<div class="grid gap-6 lg:grid-cols-2">
    <section class="rounded-lg border border-gray-200 bg-white p-5">
        <h1 class="text-lg font-semibold">User Verification</h1>
        <div class="mt-4 divide-y divide-gray-100">
            @foreach ($users as $user)
                <div class="flex justify-between py-3 text-sm"><span>{{ $user->name }}</span><span>{{ $user->role }}</span></div>
            @endforeach
        </div>
    </section>
    <section class="rounded-lg border border-gray-200 bg-white p-5">
        <h1 class="text-lg font-semibold">Payment Confirmations</h1>
        <div class="mt-4 divide-y divide-gray-100">
            @foreach ($billingSubmissions as $submission)
                <div class="flex items-center justify-between py-3 text-sm">
                    <span>{{ $submission->company->name }} - <x-money :amount="$submission->amount" /></span>
                    <form method="POST" action="{{ route('super-admin.billing.confirm', $submission) }}">@csrf<button class="rounded-md border border-gray-300 px-3 py-1 text-xs">Confirm</button></form>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
