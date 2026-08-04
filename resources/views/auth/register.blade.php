@extends('layouts.guest')

@section('content')
<h1 class="text-xl font-semibold">Daftar Paperwork</h1>
<form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-4">
    @csrf
    <x-form.input name="company_name" label="Perusahaan" />
    <x-form.input name="name" label="Nama" />
    <x-form.input name="email" label="Email" type="email" />
    <x-form.input name="password" label="Kata Sandi" type="password" />
    <x-form.input name="password_confirmation" label="Konfirmasi Kata Sandi" type="password" />
    <button class="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white">Daftar</button>
</form>
@endsection
