<div class="border-b border-gray-200 dark:border-gray-800 mb-6">
    <nav class="-mb-px flex space-x-6 overflow-x-auto custom-scrollbar">
        <a href="{{ route('settings.company') }}"
           class="whitespace-nowrap pb-3 px-1 text-sm font-medium border-b-2 transition {{ request()->routeIs('settings.company') ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
            Profil Perusahaan
        </a>
        <a href="{{ route('settings.bank-accounts') }}"
           class="whitespace-nowrap pb-3 px-1 text-sm font-medium border-b-2 transition {{ request()->routeIs('settings.bank-accounts*') ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
            Rekening Bank
        </a>
        <a href="{{ route('settings.data') }}"
           class="whitespace-nowrap pb-3 px-1 text-sm font-medium border-b-2 transition {{ request()->routeIs('settings.data*') ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
            Impor / Ekspor Data
        </a>
        <a href="{{ route('settings.billing') }}"
           class="whitespace-nowrap pb-3 px-1 text-sm font-medium border-b-2 transition {{ request()->routeIs('settings.billing*') ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
            Paket Langganan
        </a>
        <a href="{{ route('settings.security') }}"
           class="whitespace-nowrap pb-3 px-1 text-sm font-medium border-b-2 transition {{ request()->routeIs('settings.security') ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
            Keamanan
        </a>
    </nav>
</div>
