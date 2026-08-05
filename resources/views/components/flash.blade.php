@php
    $toasts = collect([
        session('success') ? ['type' => 'success', 'message' => session('success')] : null,
        session('error') ? ['type' => 'error', 'message' => session('error')] : null,
        session('warning') ? ['type' => 'warning', 'message' => session('warning')] : null,
        session('info') ? ['type' => 'info', 'message' => session('info')] : null,
        session('status') ? ['type' => 'success', 'message' => session('status')] : null,
        $errors->any() ? ['type' => 'error', 'message' => $errors->first()] : null,
    ])->filter()->values();
@endphp

{{-- Toast Container (Top Center) --}}
<div x-data="toastManager()" x-init="init()" class="fixed top-4 left-1/2 z-[100000] flex -translate-x-1/2 flex-col items-center gap-2 pointer-events-none" style="width: calc(100% - 2rem); max-width: 420px;">
    <template x-for="(toast, index) in toasts" :key="toast.id">
        <div x-show="toast.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
            class="pointer-events-auto flex w-full items-start gap-3 rounded-xl border px-4 py-3 shadow-lg backdrop-blur-md"
            :class="{
                'bg-emerald-50/95 border-emerald-200 dark:bg-emerald-950/90 dark:border-emerald-800': toast.type === 'success',
                'bg-red-50/95 border-red-200 dark:bg-red-950/90 dark:border-red-800': toast.type === 'error',
                'bg-amber-50/95 border-amber-200 dark:bg-amber-950/90 dark:border-amber-800': toast.type === 'warning',
                'bg-blue-50/95 border-blue-200 dark:bg-blue-950/90 dark:border-blue-800': toast.type === 'info',
            }"
        >
            {{-- Icon --}}
            <div class="mt-0.5 shrink-0">
                <template x-if="toast.type === 'success'">
                    <svg class="h-5 w-5 text-emerald-500 dark:text-emerald-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="h-5 w-5 text-red-500 dark:text-red-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                </template>
                <template x-if="toast.type === 'warning'">
                    <svg class="h-5 w-5 text-amber-500 dark:text-amber-400" viewBox="0 0 24 24" fill="currentColor"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                </template>
                <template x-if="toast.type === 'info'">
                    <svg class="h-5 w-5 text-blue-500 dark:text-blue-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                </template>
            </div>

            {{-- Message --}}
            <p class="flex-1 text-sm font-medium leading-snug"
                :class="{
                    'text-emerald-800 dark:text-emerald-200': toast.type === 'success',
                    'text-red-800 dark:text-red-200': toast.type === 'error',
                    'text-amber-800 dark:text-amber-200': toast.type === 'warning',
                    'text-blue-800 dark:text-blue-200': toast.type === 'info',
                }"
                x-text="toast.message"></p>

            {{-- Close Button --}}
            <button @click="dismiss(toast.id)" class="shrink-0 rounded-lg p-0.5 transition hover:bg-black/5 dark:hover:bg-white/10"
                :class="{
                    'text-emerald-400 hover:text-emerald-600 dark:text-emerald-500': toast.type === 'success',
                    'text-red-400 hover:text-red-600 dark:text-red-500': toast.type === 'error',
                    'text-amber-400 hover:text-amber-600 dark:text-amber-500': toast.type === 'warning',
                    'text-blue-400 hover:text-blue-600 dark:text-blue-500': toast.type === 'info',
                }"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            </button>
        </div>
    </template>
</div>

@if ($toasts->isNotEmpty())
    <script type="application/json" data-toast-payload>
        {!! $toasts->toJson(JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
    </script>
@endif

@once
<script>
function toastManager() {
    let _id = 0;
    return {
        toasts: [],

        init() {
            if (window.__flashToastManagerInitialized) {
                return;
            }

            window.__flashToastManagerInitialized = true;

            // Read server-side flash toasts
            const el = document.querySelector('[data-toast-payload]');
            if (el) {
                try {
                    const payload = JSON.parse(el.textContent);
                    payload
                        .filter((toast, index, list) => index === list.findIndex((item) => item.type === toast.type && item.message === toast.message))
                        .forEach(t => this.show(t.type, t.message));
                } catch (e) { console.error('Toast parse error', e); }
            }

            // Listen for custom JS toast events (for AJAX calls)
            window.addEventListener('toast', (e) => {
                this.show(e.detail.type || 'info', e.detail.message || '');
            });
        },

        show(type, message, duration = 4000) {
            const normalizedType = ['success', 'error', 'warning', 'info'].includes(type) ? type : 'info';
            const normalizedMessage = String(message || '').trim();

            if (!normalizedMessage) {
                return;
            }

            if (this.toasts.some((toast) => toast.type === normalizedType && toast.message === normalizedMessage)) {
                return;
            }

            const id = ++_id;
            this.toasts.push({ id, type: normalizedType, message: normalizedMessage, visible: true });
            if (duration > 0) {
                setTimeout(() => this.dismiss(id), duration);
            }
        },

        dismiss(id) {
            const t = this.toasts.find(t => t.id === id);
            if (t) t.visible = false;
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 300);
        }
    };
}
</script>
@endonce
