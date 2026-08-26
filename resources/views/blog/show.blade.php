@extends('layouts.public')

@section('title', $post->meta_title ?: $post->title)
@section('meta_description', $post->meta_description ?: $post->excerpt)

@section('content')
<article class="py-16 md:py-24 px-4 sm:px-6 lg:px-8 mx-auto max-w-3xl">
    <header class="mb-10 text-center">
        <time datetime="{{ $post->published_at->toDateString() }}" class="text-sm text-gray-500 dark:text-gray-400 block mb-4">
            {{ $post->published_at->format('d F Y') }}
        </time>
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight text-gray-900 dark:text-white mb-6">
            {{ $post->title }}
        </h1>
        @if($post->author)
        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Ditulis oleh {{ $post->author->name }}
        </div>
        @endif
    </header>

    <div class="prose prose-brand dark:prose-invert prose-lg mx-auto">
        {!! nl2br(e($post->content)) !!}
    </div>

    <div class="mt-16 pt-8 border-t border-gray-200 dark:border-gray-800 text-center">
        <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-brand-600 hover:text-brand-700 dark:text-brand-400">
            &larr; Kembali ke Blog
        </a>
    </div>
</article>
@endsection
