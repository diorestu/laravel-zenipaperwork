@extends('layouts.guest')

@section('content')
<h1 class="text-xl font-semibold">Lupa Kata Sandi</h1>
<p class="mt-2 text-sm text-gray-600">Masukkan email untuk mengatur ulang kata sandi.</p>
<form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
    @csrf
    <x-form.input name="email" label="Email" type="email" />
    <button class="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white">Kirim Link Reset</button>
</form>
@endsection
