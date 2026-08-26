@extends('layouts.public')

@section('title', 'Paperwork Blog - Tips & Panduan Bisnis')

@section('content')
<div class="py-16 md:py-24 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl dark:text-white mb-4">Blog Paperwork</h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">Tips, pembaruan produk, dan panduan lengkap seputar pengelolaan bisnis, invoice, dan keuangan.</p>
    </div>
</div>

<div class="py-12 bg-[#FBFBFE] dark:bg-gray-950">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($posts as $post)
            <article class="flex flex-col items-start justify-between rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-brand-500 hover:shadow-theme-md dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-x-4 text-xs">
                    <time datetime="{{ $post->published_at->toDateString() }}" class="text-gray-500 dark:text-gray-400">{{ $post->published_at->format('d M Y') }}</time>
                </div>
                <div class="group relative mt-3 w-full">
                    <h3 class="mt-3 text-lg font-bold leading-6 text-gray-900 dark:text-white group-hover:text-brand-600">
                        <a href="{{ route('blog.show', $post->slug) }}">
                            <span class="absolute inset-0"></span>
                            {{ $post->title }}
                        </a>
                    </h3>
                    <p class="mt-3 line-clamp-3 text-sm leading-6 text-gray-600 dark:text-gray-400">{{ $post->excerpt }}</p>
                </div>
            </article>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500">Belum ada artikel saat ini.</p>
            </div>
            @endforelse
        </div>
        
        <div class="mt-10">
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection
