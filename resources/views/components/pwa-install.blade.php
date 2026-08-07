<div
    x-data="pwaInstaller()"
    x-init="initPWA()"
    x-show="showBanner"
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-6 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 translate-y-6 scale-95"
    class="fixed bottom-4 left-4 right-4 z-[99999] mx-auto max-w-sm rounded-2xl border border-gray-200/90 bg-white/95 p-4 shadow-2xl backdrop-blur-xl dark:border-gray-800 dark:bg-gray-900/95 sm:bottom-6 sm:left-auto sm:right-6"
    style="display: none;"
>
    <!-- Toast Header & App Icon -->
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gray-900 p-1.5 shadow-sm dark:bg-white/10">
                <img src="{{ asset('favicon.png') }}" alt="Paperwork App" class="h-8 w-8 object-contain rounded-lg">
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-950 dark:text-white">Pasang Aplikasi Paperwork</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Tambahkan ke Layar Utama HP untuk akses cepat & notifikasi</p>
            </div>
        </div>
        <button @click="dismiss()" aria-label="Tutup" class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-white/10">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- iOS Instructions Accordion -->
    <div x-show="isIOS" class="mt-3 rounded-xl border border-amber-200/60 bg-amber-50/60 p-3 text-xs text-amber-900 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-300">
        <p class="font-semibold text-amber-950 dark:text-amber-200 mb-1">Cara pasang di iPhone / iPad:</p>
        <ol class="list-decimal space-y-1 pl-4 leading-relaxed text-[11px]">
            <li>Tekan tombol <strong>Share</strong> (Ikon Kotak + Panah) di Safari.</li>
            <li>Pilih <strong>"Tambahkan ke Layar Utama"</strong>.</li>
            <li>Tekan <strong>"Tambah"</strong> di kanan atas.</li>
        </ol>
    </div>

    <!-- Action Toast Buttons -->
    <div class="mt-3 flex items-center gap-2">
        <button
            x-show="!isIOS"
            @click="installAndroid()"
            class="flex-1 flex h-9 items-center justify-center gap-1.5 rounded-xl bg-brand-600 px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-brand-700 active:scale-[0.98] dark:bg-brand-500 dark:hover:bg-brand-600"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Pasang Sekarang
        </button>
        <button
            @click="dismiss(true)"
            class="flex h-9 items-center justify-center rounded-xl border border-gray-200 bg-gray-50 px-3 text-xs font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
        >
            Nanti
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

            // Detect Mobile & iOS
            const ua = window.navigator.userAgent;
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua) || window.innerWidth < 768;
            this.isIOS = /iPhone|iPad|iPod/i.test(ua);

            // Register Service Worker
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js').then((reg) => {
                    console.log('[SW] Service Worker registered successfully:', reg.scope);
                }).catch(err => console.error('[SW] Registration Error:', err));
            }

            window.__paperworkVapidPublicKey = this.vapidPublicKey;
            this.setupWebPushSubscription();

            // Listen for Android/Chrome beforeinstallprompt
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                this.deferredPrompt = e;
                this.showBanner = true;
            });

            // Always show prompt banner on mobile after 1.5s unless dismissed
            if (isMobile && localStorage.getItem('pwa_dismissed') !== 'true') {
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
                        if (window.toast) window.toast('success', 'Paperwork berhasil dipasang ke Layar Utama.');
                    }
                    this.deferredPrompt = null;
                });
            } else {
                if (window.toast) {
                    window.toast('info', 'Silakan gunakan opsi "Tambahkan ke Layar Utama" pada menu browser Anda.');
                } else {
                    alert('Silakan gunakan opsi "Tambahkan ke Layar Utama" pada menu browser Anda.');
                }
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
