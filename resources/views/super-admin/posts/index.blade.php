@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-title-md2 font-bold text-black dark:text-white">Blog Posts</h2>
    <a href="{{ route('super-admin.posts.create') }}" class="inline-flex items-center justify-center gap-2.5 rounded-md bg-brand-500 px-10 py-3 text-center font-medium text-white hover:bg-opacity-90">
        Create Post
    </a>
</div>

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="max-w-full overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="bg-gray-2 text-left dark:bg-meta-4">
                    <th class="min-w-[220px] py-4 px-4 font-medium text-black dark:text-white">Title</th>
                    <th class="min-w-[150px] py-4 px-4 font-medium text-black dark:text-white">Status</th>
                    <th class="min-w-[120px] py-4 px-4 font-medium text-black dark:text-white">Published At</th>
                    <th class="py-4 px-4 font-medium text-black dark:text-white text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr>
                    <td class="border-b border-[#eee] py-5 px-4 dark:border-strokedark">
                        <p class="text-black dark:text-white">{{ $post->title }}</p>
                    </td>
                    <td class="border-b border-[#eee] py-5 px-4 dark:border-strokedark">
                        <p class="inline-flex rounded-full bg-opacity-10 py-1 px-3 text-sm font-medium {{ $post->is_published ? 'bg-success text-success' : 'bg-warning text-warning' }}">
                            {{ $post->is_published ? 'Published' : 'Draft' }}
                        </p>
                    </td>
                    <td class="border-b border-[#eee] py-5 px-4 dark:border-strokedark">
                        <p class="text-black dark:text-white">{{ $post->published_at ? $post->published_at->format('M d, Y') : '-' }}</p>
                    </td>
                    <td class="border-b border-[#eee] py-5 px-4 dark:border-strokedark">
                        <div class="flex items-center justify-end space-x-3.5">
                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="hover:text-primary">View</a>
                            <a href="{{ route('super-admin.posts.edit', $post) }}" class="hover:text-primary">Edit</a>
                            <form action="{{ route('super-admin.posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('Delete this post?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="hover:text-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-5 px-4 text-center">No posts found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-stroke dark:border-strokedark">
        {{ $posts->links() }}
    </div>
</div>
@endsection
