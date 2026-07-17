@extends('layouts.guest')

@section('content')
<h1 class="text-xl font-semibold">Verify Email</h1>
<p class="mt-2 text-sm text-gray-600">Cek email untuk link verifikasi akun Paperwork.</p>
<form method="POST" action="{{ route('verification.send') }}" class="mt-6">
    @csrf
    <button class="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white">Kirim Ulang Verification</button>
</form>
@endsection
