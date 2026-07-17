@extends('layouts.guest')

@section('content')
<h1 class="text-xl font-semibold">Reset Password</h1>
<form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
    @csrf
    <input type="hidden" name="token" value="{{ request()->route('token') }}">
    <x-form.input name="email" label="Email" type="email" />
    <x-form.input name="password" label="Password" type="password" />
    <x-form.input name="password_confirmation" label="Confirm Password" type="password" />
    <button class="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white">Reset</button>
</form>
@endsection
