@extends('layouts.fullscreen-layout')

@section('content')
@vite('resources/css/mobile-pwa.css')

@php
    $dashboardUrl = auth()->check() ? route('mobile.app') : route('login');
    $primaryLabel = auth()->check() ? 'Buka Aplikasi' : 'Masuk';
@endphp

<main class="mobile-pwa" x-data="mobileInstallPage()" x-init="init()">
    <nav class="mobile-pwa__nav" aria-label="Navigasi mobile">
        <a class="mobile-pwa__brand" href="{{ url('/') }}" aria-label="Paperwork">
            <img src="{{ asset('images/logo/paperwork-logo.png') }}" alt="" width="28" height="28" class="dark:hidden">
            <img src="{{ asset('img/logo/logo_white.png') }}" alt="" width="28" height="28" class="hidden dark:block">
            <span>Paperwork</span>
        </a>
        <a class="mobile-pwa__nav-action" href="{{ $dashboardUrl }}">{{ $primaryLabel }}</a>
    </nav>

    <section class="mobile-pwa__hero reveal" style="--i: 0">
        <div class="mobile-pwa__hero-copy">
            <p class="mobile-pwa__status">PWA untuk operasional mobile</p>
            <h1>Kerja invoice dari layar HP.</h1>
            <p>
                Pasang Paperwork ke layar utama untuk membuka invoice, klien,
                penawaran, dan pembayaran tanpa melewati dashboard desktop.
            </p>
            <div class="mobile-pwa__hero-actions" aria-label="Aksi utama">
                <button type="button" class="mobile-pwa__button mobile-pwa__button--primary" x-show="canInstall" @click="installApp()">
                    Pasang Aplikasi
                </button>
                <a class="mobile-pwa__button mobile-pwa__button--primary" x-show="!canInstall" href="{{ $dashboardUrl }}">
                    {{ $primaryLabel }}
                </a>
                <a class="mobile-pwa__button mobile-pwa__button--ghost" href="{{ route('login') }}">
                    Login Web
                </a>
            </div>
        </div>

        <section class="mobile-pwa__workbench" aria-label="Preview workspace mobile">
            <div class="mobile-pwa__sheet mobile-pwa__sheet--summary">
                <div>
                    <span class="mobile-pwa__muted">Saldo belum dibayar</span>
                    <strong>Rp 12.400.000</strong>
                </div>
                <span class="mobile-pwa__badge">3 invoice aktif</span>
            </div>

            <div class="mobile-pwa__sheet mobile-pwa__sheet--invoice">
                <div class="mobile-pwa__row">
                    <div>
                        <span class="mobile-pwa__muted">INV-2026-014</span>
                        <strong>PT Numa Teknologi Nusantara</strong>
                    </div>
                    <span class="mobile-pwa__dot" aria-hidden="true"></span>
                </div>
                <div class="mobile-pwa__progress" aria-hidden="true"><span></span></div>
                <div class="mobile-pwa__row mobile-pwa__row--compact">
                    <span>Termin 2</span>
                    <strong>Rp 4.800.000</strong>
                </div>
            </div>

            <div class="mobile-pwa__quick-grid" aria-label="Aksi cepat">
                <a href="{{ $dashboardUrl }}">Invoice</a>
                <a href="{{ $dashboardUrl }}">Klien</a>
                <a href="{{ $dashboardUrl }}">Produk</a>
                <a href="{{ $dashboardUrl }}">Billing</a>
            </div>
        </section>
    </section>

    <section class="mobile-pwa__steps reveal" style="--i: 1" aria-labelledby="install-title">
        <div class="mobile-pwa__section-head">
            <h2 id="install-title">Pasang sekali, pakai setiap hari.</h2>
            <p>Browser tetap menjadi mesin aplikasi. Ikon Paperwork muncul di layar utama seperti aplikasi native.</p>
        </div>

        <div class="mobile-pwa__step-list">
            <article class="mobile-pwa__step-card">
                <span class="mobile-pwa__step-number">Safari</span>
                <h3>Untuk iPhone dan iPad</h3>
                <ol>
                    <li>Buka halaman ini melalui Safari.</li>
                    <li>Tekan Bagikan di bilah bawah.</li>
                    <li>Pilih Tambahkan ke Layar Utama.</li>
                    <li>Tekan Tambah.</li>
                </ol>
            </article>

            <article class="mobile-pwa__step-card">
                <span class="mobile-pwa__step-number">Chrome</span>
                <h3>Untuk Android</h3>
                <ol>
                    <li>Buka halaman ini melalui Chrome.</li>
                    <li>Tekan menu titik tiga.</li>
                    <li>Pilih Install Application atau Tambahkan ke Layar Utama.</li>
                    <li>Buka Paperwork dari ikon baru.</li>
                </ol>
            </article>
        </div>
    </section>

    <section class="mobile-pwa__utility reveal" style="--i: 2" aria-label="Fitur mobile">
        <a href="{{ $dashboardUrl }}">
            <span>Ringkasan</span>
            <strong>Cek nilai invoice, piutang, dan dokumen terbaru.</strong>
        </a>
        <a href="{{ $dashboardUrl }}">
            <span>Dokumen</span>
            <strong>Buat invoice atau penawaran tanpa membuka layout desktop.</strong>
        </a>
        <a href="{{ $dashboardUrl }}">
            <span>Kontak</span>
            <strong>Akses klien dan tautan WhatsApp langsung dari HP.</strong>
        </a>
    </section>

    <aside class="mobile-pwa__sticky" aria-label="Aksi instalasi">
        <span x-text="installHint"></span>
        <button type="button" x-show="canInstall" @click="installApp()">Pasang</button>
        <a x-show="!canInstall" href="{{ $dashboardUrl }}">{{ $primaryLabel }}</a>
    </aside>
</main>

<script>
function mobileInstallPage() {
    return {
        deferredPrompt: null,
        canInstall: false,
        installHint: 'Buka dari browser mobile untuk memasang PWA.',
        init() {
            window.addEventListener('beforeinstallprompt', (event) => {
                event.preventDefault();
                this.deferredPrompt = event;
                this.canInstall = true;
                this.installHint = 'Paperwork siap dipasang ke layar utama.';
            });

            const standalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
            if (standalone) {
                this.installHint = 'Paperwork sudah berjalan sebagai aplikasi.';
            }
        },
        async installApp() {
            if (!this.deferredPrompt) {
                this.installHint = 'Gunakan menu browser untuk menambahkan ke layar utama.';
                return;
            }

            this.deferredPrompt.prompt();
            const choice = await this.deferredPrompt.userChoice;
            this.canInstall = false;
            this.deferredPrompt = null;
            this.installHint = choice.outcome === 'accepted'
                ? 'Paperwork ditambahkan ke layar utama.'
                : 'Instalasi dibatalkan. Anda masih bisa memasang dari menu browser.';
        }
    }
}
</script>
@endsection
