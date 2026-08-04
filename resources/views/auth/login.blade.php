@extends('layouts.guest')

@section('content')
<h1 class="text-xl font-semibold">Masuk Paperwork</h1>
<form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
    @csrf
    <x-form.input name="email" label="Email" type="email" />
    <x-form.input name="password" label="Kata Sandi" type="password" />
    <button class="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white">Masuk</button>
</form>
<a href="{{ route('register') }}" class="mt-4 block text-sm text-gray-600">Daftarkan perusahaan</a>
@endsection
