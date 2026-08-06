<div
    x-data="pwaInstaller()"
    x-init="initPWA()"
    x-show="showBanner"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    class="fixed bottom-4 left-4 right-4 z-50 mx-auto max-w-md rounded-2xl border border-gray-200/80 bg-white/95 p-5 shadow-2xl backdrop-blur-xl dark:border-white/10 dark:bg-gray-900/95 sm:bottom-6"
    style="display: none;"
>
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gray-950 p-2 shadow-sm dark:bg-white/10">
                <img src="{{ asset('images/logo/paperwork-logo.png') }}" alt="Paperwork App" class="h-8 w-8 object-contain dark:hidden">
                <img src="{{ asset('img/logo/logo_white.png') }}" alt="Paperwork App" class="hidden h-8 w-8 object-contain dark:block">
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-950 dark:text-white">Paperwork Mobile</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Pasang aplikasi di Layar Utama HP Anda</p>
            </div>
        </div>
        <button @click="dismiss()" class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-white/10">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- iOS Instructions -->
    <div x-show="isIOS" class="mt-4 rounded-xl border border-amber-200/60 bg-amber-50/60 p-3.5 text-xs text-amber-900 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-300">
        <p class="font-semibold text-amber-950 dark:text-amber-200 mb-1.5">Cara pasang di iPhone / iPad:</p>
        <ol class="list-decimal space-y-1 pl-4 leading-relaxed">
            <li>Tekan tombol <strong>Share</strong> (Ikon Kotak + Panah ke atas <svg class="inline-block h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>) di Safari.</li>
            <li>Geser ke bawah dan pilih <strong>"Tambahkan ke Layar Utama"</strong> (Add to Home Screen).</li>
            <li>Tekan <strong>"Tambah"</strong> di kanan atas.</li>
        </ol>
    </div>

    <!-- Android Install Button -->
    <div x-show="!isIOS" class="mt-4">
        <button
            @click="installAndroid()"
            class="flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-gray-950 text-sm font-semibold text-white shadow-md transition hover:bg-gray-800 active:scale-[0.98] dark:bg-white dark:text-gray-950 dark:hover:bg-gray-200"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Pasang Aplikasi Sekarang
        </button>
    </div>

    <div class="mt-3 flex items-center justify-between text-xs">
        <span class="text-gray-400">Gratis & Tanpa Toko Aplikasi</span>
        <button @click="dismiss(true)" class="font-medium text-gray-500 hover:text-gray-800 dark:hover:text-gray-300 underline">
            Jangan Tampilkan Lagi
        </button>
    </div>
</div>

<script>
function pwaInstaller() {
    return {
        showBanner: false,
        isIOS: false,
        deferredPrompt: null,
        vapidPublicKey: @js(config('services.firebase.web_push_vapid_public_key') ?: 'BE7pt7H3ZAiQ__kn1M_uv4mFOdfDQlDGCfWS_UdCDsaqEzZdmSjP-TLYQXJwCc_6q5zhZ41t6RWq35ek-wvPRyI'),

        initPWA() {
            // Check if already running in standalone mode (installed as PWA)
            const isStandalone = window.navigator.standalone || window.matchMedia('(display-mode: standalone)').matches;
            if (isStandalone) return;

            // Check if user permanently dismissed
            if (localStorage.getItem('pwa_dismissed') === 'true') return;

            // Detect Mobile
            const ua = window.navigator.userAgent;
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua) || window.innerWidth < 768;
            this.isIOS = /iPhone|iPad|iPod/i.test(ua);

            if (isMobile) {
                // Register Service Worker
                if ('serviceWorker' in navigator) {
                    navigator.serviceWorker.register('/sw.js').catch(err => console.error('SW Error:', err));
                }

                window.__paperworkVapidPublicKey = this.vapidPublicKey;
                this.setupWebPushSubscription();

                // Listen for Android beforeinstallprompt
                window.addEventListener('beforeinstallprompt', (e) => {
                    e.preventDefault();
                    this.deferredPrompt = e;
                    this.showBanner = true;
                });

                // Show for iOS or general mobile after small delay
                setTimeout(() => {
                    this.showBanner = true;
                }, 1500);
            }
        },

        installAndroid() {
            if (this.deferredPrompt) {
                this.deferredPrompt.prompt();
                this.deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        this.showBanner = false;
                    }
                    this.deferredPrompt = null;
                });
            } else {
                alert('Silakan gunakan opsi "Tambahkan ke Layar Utama" pada menu browser Anda.');
            }
        },

        urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/-/g, '+')
                .replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);

            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }

            return outputArray;
        },

        async setupWebPushSubscription() {
            if (!('Notification' in window) || !('serviceWorker' in navigator) || !('PushManager' in window)) {
                return;
            }

            if (Notification.permission === 'denied') {
                return;
            }

            try {
                const registration = await navigator.serviceWorker.ready;
                let permission = Notification.permission;

                if (permission === 'default') {
                    permission = await Notification.requestPermission();
                }

                if (permission !== 'granted') {
                    return;
                }

                let subscription = await registration.pushManager.getSubscription();
                if (!subscription) {
                    subscription = await registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey),
                    });
                }

                await fetch('{{ url('/api/v1/device-tokens') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        token: JSON.stringify(subscription),
                        device_type: 'web',
                        device_name: navigator.userAgent,
                    }),
                });
            } catch (error) {
                console.error('Web Push subscription failed:', error);
            }
        },

        dismiss(permanent = false) {
            this.showBanner = false;
            if (permanent) {
                localStorage.setItem('pwa_dismissed', 'true');
            }
        }
    }
}
</script>
