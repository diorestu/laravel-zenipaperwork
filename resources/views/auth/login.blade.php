@extends('layouts.guest')

@section('content')
<h1 class="text-xl font-semibold">Login Paperwork</h1>
<form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
    @csrf
    <x-form.input name="email" label="Email" type="email" />
    <x-form.input name="password" label="Password" type="password" />
    <button class="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white">Login</button>
</form>
<a href="{{ route('register') }}" class="mt-4 block text-sm text-gray-600">Register company</a>
@endsection
