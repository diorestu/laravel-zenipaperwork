@extends('layouts.guest')

@section('content')
<h1 class="text-xl font-semibold">Register Paperwork</h1>
<form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-4">
    @csrf
    <x-form.input name="company_name" label="Company" />
    <x-form.input name="name" label="Name" />
    <x-form.input name="email" label="Email" type="email" />
    <x-form.input name="password" label="Password" type="password" />
    <x-form.input name="password_confirmation" label="Confirm Password" type="password" />
    <button class="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white">Register</button>
</form>
@endsection
