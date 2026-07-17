{{-- Notification Dropdown Component --}}
@php
    $notifications ??= collect();
    $unreadCount ??= 0;
    $hasUnread ??= false;
@endphp

<div class="relative" x-data="{
    dropdownOpen: false,
    notifying: {{ $hasUnread ? 'true' : 'false' }},
    toggleDropdown() {
        this.dropdownOpen = !this.dropdownOpen;
        this.notifying = false;
    },
    closeDropdown() {
        this.dropdownOpen = false;
    }
}" @click.away="closeDropdown()">
    <!-- Notification Button -->
    <button
        class="relative flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white cursor-pointer"
        @click="toggleDropdown()"
        type="button"
    >
        <!-- Notification Badge -->
        <span
            x-show="notifying"
            class="absolute right-0 top-0.5 z-1 h-2 w-2 rounded-full bg-orange-400"
        >
            <span
                class="absolute inline-flex w-full h-full bg-orange-400 rounded-full opacity-75 -z-1 animate-ping"
            ></span>
        </span>

        <!-- Bell Icon -->
        <svg
            class="fill-current"
            width="20"
            height="20"
            viewBox="0 0 20 20"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z"
                fill=""
            />
        </svg>
    </button>

    <!-- Dropdown Start -->
    <div
        x-show="dropdownOpen"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute -right-[240px] mt-[17px] flex h-[480px] w-[350px] flex-col rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark sm:w-[361px] lg:right-0 z-50"
        style="display: none;"
    >
        <!-- Dropdown Header -->
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100 dark:border-gray-800">
            <h5 class="text-sm font-semibold text-gray-800 dark:text-white/90">Notifikasi ({{ $unreadCount }})</h5>

            @if($hasUnread)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-xs text-brand-600 hover:text-brand-700 dark:text-brand-400 font-semibold cursor-pointer">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        <!-- Notification List -->
        <ul class="flex flex-col h-auto overflow-y-auto custom-scrollbar space-y-1.5 flex-grow">
            @forelse ($notifications as $notification)
                <li class="border-b border-gray-50 last:border-0 dark:border-gray-800/40">
                    <div class="flex flex-col gap-1 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 {{ $notification->read_at ? 'opacity-65' : 'bg-brand-500/[0.04]' }}">
                        <div class="flex justify-between items-start gap-2">
                            <span class="font-semibold text-xs text-gray-900 dark:text-white">{{ $notification->title }}</span>
                            <span class="text-[10px] text-gray-400 whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed">{{ $notification->body }}</p>
                    </div>
                </li>
            @empty
                <li class="py-24 text-center text-xs text-gray-400 flex flex-col items-center justify-center flex-grow">
                    <i class="bx bx-bell-off text-3xl mb-3 text-gray-300 dark:text-gray-700"></i>
                    <span>Tidak ada notifikasi baru.</span>
                </li>
            @endforelse
        </ul>

        <!-- View All Button -->
        <a
            href="{{ route('notifications.index') }}"
            class="mt-3 flex justify-center rounded-lg border border-gray-300 bg-white p-2.5 text-xs font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
            @click="closeDropdown()"
        >
            Lihat Semua Notifikasi
        </a>
    </div>
    <!-- Dropdown End -->
</div>
