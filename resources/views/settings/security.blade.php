@extends('layouts.app')

@section('content')
<div class="max-w-4xl space-y-6">
    @include('settings.partials.settings-nav')

    <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-white/90">Keamanan</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Kata sandi, verifikasi email, dan kebijakan sesi dikelola oleh autentikasi Laravel.</p>
    </section>
</div>
@endsection
