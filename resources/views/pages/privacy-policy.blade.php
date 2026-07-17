@extends('layouts.fullscreen-layout', ['title' => 'Kebijakan Privasi'])

@section('content')
    <main class="min-h-screen bg-[#F7F6F3] text-[#111111] dark:bg-gray-950 dark:text-white">
        <div class="mx-auto flex min-h-screen w-full max-w-4xl flex-col px-6 py-12 sm:px-8 lg:px-10">
            <header class="flex items-center justify-between border-b border-[#EAEAEA] pb-6 dark:border-white/10">
                <a href="/" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                    <span class="flex size-8 items-center justify-center rounded-md border border-gray-200 bg-white text-sm font-bold dark:border-white/10 dark:bg-white/5">P</span>
                    Paperwork
                </a>

                <div class="flex items-center gap-4">
                    <a href="{{ route('signin') }}" class="text-sm font-medium text-gray-500 transition hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                        Masuk
                    </a>
                    <a href="{{ route('signup') }}" class="rounded-lg bg-[#111111] px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-[#333333] dark:bg-white dark:text-gray-950 dark:hover:bg-gray-200">
                        Daftar
                    </a>
                </div>
            </header>

            <article class="prose dark:prose-invert mt-10 max-w-none text-gray-600 dark:text-gray-400">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">
                    Kebijakan Privasi
                </p>
                <h1 class="mt-2 text-4xl font-bold tracking-tight text-gray-950 dark:text-white">
                    Kebijakan Privasi Paperwork
                </h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Terakhir diperbarui: 3 Juli 2026
                </p>

                <div class="mt-8 space-y-8 text-base leading-7">
                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold text-gray-950 dark:text-white">1. Informasi Yang Kami Kumpulkan</h2>
                        <p>Kami mengumpulkan informasi untuk memberikan layanan yang lebih baik kepada seluruh pengguna kami. Informasi yang kami kumpulkan meliputi:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li><strong>Informasi Akun:</strong> Nama lengkap, alamat email, kata sandi yang disandikan (hash), dan nama perusahaan Anda saat mendaftar.</li>
                            <li><strong>Data Dokumen & Keuangan:</strong> Data klien, detail produk/layanan, quotation, invoice, dan data pembayaran yang Anda masukkan ke dalam platform.</li>
                            <li><strong>Informasi Log & Teknis:</strong> Alamat IP, jenis peramban (browser), waktu akses, dan interaksi Anda dengan platform kami.</li>
                        </ul>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold text-gray-950 dark:text-white">2. Bagaimana Kami Menggunakan Informasi</h2>
                        <p>Kami menggunakan data yang dikumpulkan untuk tujuan berikut:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Menyediakan, memelihara, dan mengoptimalkan platform Paperwork.</li>
                            <li>Memproses pembuatan dokumen seperti quotation, invoice, dan pencatatan pembayaran secara akurat.</li>
                            <li>Mengirimkan notifikasi penting terkait aktivitas akun, verifikasi email, keamanan, dan pembaruan sistem.</li>
                            <li>Melakukan analisis statistik internal untuk meningkatkan pengalaman pengguna.</li>
                        </ul>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold text-gray-950 dark:text-white">3. Keamanan Data Anda</h2>
                        <p>Keamanan data Anda adalah prioritas utama kami. Kami menerapkan langkah-langkah keamanan teknis dan organisasi yang dirancang untuk melindungi data Anda dari akses, penggunaan, perubahan, atau pengungkapan yang tidak sah.</p>
                        <p>Semua lalu lintas data dienkripsi menggunakan protokol HTTPS, dan kata sandi Anda disimpan menggunakan fungsi hash yang aman.</p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold text-gray-950 dark:text-white">4. Berbagi Informasi Dengan Pihak Ketiga</h2>
                        <p>Kami tidak menjual, menyewakan, atau memperdagangkan data pribadi Anda kepada pihak ketiga. Kami hanya membagikan data dengan layanan pihak ketiga tepercaya yang diperlukan untuk operasional aplikasi (seperti layanan hosting, pengiriman email, dan gerbang pembayaran/payment gateway), dengan tunduk pada kepatuhan kebijakan privasi yang ketat.</p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold text-gray-950 dark:text-white">5. Hak Anda Sebagai Pengguna</h2>
                        <p>Anda memiliki hak penuh untuk mengakses, memperbarui, atau menghapus informasi profil dan data bisnis Anda langsung melalui halaman pengaturan akun di platform kami kapan saja.</p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold text-gray-950 dark:text-white">6. Hubungi Kami</h2>
                        <p>Jika Anda memiliki pertanyaan atau kekhawatiran mengenai Kebijakan Privasi ini, silakan hubungi kami melalui email di: <a href="mailto:support@paperwork.id" class="font-medium text-gray-950 underline dark:text-white">support@paperwork.id</a>.</p>
                    </section>
                </div>
            </article>

            <footer class="mt-16 border-t border-[#EAEAEA] pt-6 text-center text-xs text-gray-500 dark:border-white/10 dark:text-gray-400">
                <p>&copy; {{ now()->year }} Paperwork. Seluruh hak cipta dilindungi.</p>
            </footer>
        </div>
    </main>
@endsection
