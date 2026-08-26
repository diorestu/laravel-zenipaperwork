@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black dark:text-white">
        {{ isset($post) ? 'Edit Post' : 'Create Post' }}
    </h2>
</div>

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <form action="{{ isset($post) ? route('super-admin.posts.update', $post) : route('super-admin.posts.store') }}" method="POST">
        @csrf
        @if(isset($post)) @method('PUT') @endif

        <div class="p-6.5">
            <div class="mb-4.5">
                <label class="mb-2.5 block text-black dark:text-white">Title <span class="text-meta-1">*</span></label>
                <input type="text" name="title" value="{{ old('title', $post->title ?? '') }}" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
            </div>

            <div class="mb-4.5">
                <label class="mb-2.5 block text-black dark:text-white">Slug (Optional)</label>
                <input type="text" name="slug" value="{{ old('slug', $post->slug ?? '') }}" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
            </div>

            <div class="mb-4.5">
                <label class="mb-2.5 block text-black dark:text-white">Excerpt</label>
                <textarea name="excerpt" rows="2" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
            </div>

            <div class="mb-4.5">
                <label class="mb-2.5 block text-black dark:text-white">Content <span class="text-meta-1">*</span></label>
                <textarea name="content" rows="10" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">{{ old('content', $post->content ?? '') }}</textarea>
            </div>

            <div class="mb-4.5">
                <label class="mb-2.5 block text-black dark:text-white">Meta Title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $post->meta_title ?? '') }}" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
            </div>

            <div class="mb-4.5">
                <label class="mb-2.5 block text-black dark:text-white">Meta Description</label>
                <textarea name="meta_description" rows="2" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
            </div>

            <div class="mb-4.5 flex items-center gap-3">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" id="is_published" {{ old('is_published', $post->is_published ?? false) ? 'checked' : '' }} class="h-5 w-5">
                <label for="is_published" class="text-black dark:text-white cursor-pointer">Publish this post</label>
            </div>

            <button type="submit" class="flex w-full justify-center rounded bg-primary p-3 font-medium text-gray hover:bg-opacity-90">
                Save Post
            </button>
        </div>
    </form>
</div>
@endsection
