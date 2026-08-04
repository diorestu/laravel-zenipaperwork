@extends('layouts.fullscreen-layout', ['title' => 'Ketentuan Pelanggan'])

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
                    Syarat & Ketentuan
                </p>
                <h1 class="mt-2 text-4xl font-bold tracking-tight text-gray-950 dark:text-white">
                    Ketentuan Pelanggan Paperwork
                </h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Terakhir diperbarui: 3 Juli 2026
                </p>

                <div class="mt-8 space-y-8 text-base leading-7">
                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold text-gray-950 dark:text-white">1. Penerimaan Ketentuan</h2>
                        <p>Dengan mendaftar, mengakses, atau menggunakan platform Paperwork ("Layanan"), Anda menyatakan bahwa Anda telah membaca, memahami, dan menyetujui untuk terikat oleh Ketentuan Pelanggan ini. Jika Anda tidak menyetujui ketentuan ini, Anda tidak diperkenankan menggunakan Layanan kami.</p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold text-gray-950 dark:text-white">2. Registrasi dan Keamanan Akun</h2>
                        <p>Untuk menggunakan Layanan, Anda wajib membuat akun dengan memberikan informasi yang akurat, lengkap, dan terbaru. Anda bertanggung jawab penuh untuk:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Menjaga kerahasiaan kata sandi akun Anda.</li>
                            <li>Semua aktivitas yang terjadi di bawah akun Anda.</li>
                            <li>Segera memberitahu kami apabila terdapat penggunaan tidak sah atas akun Anda.</li>
                        </ul>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold text-gray-950 dark:text-white">3. Penggunaan Layanan Yang Diizinkan</h2>
                        <p>Anda setuju untuk menggunakan Layanan hanya untuk tujuan bisnis yang sah dan sesuai dengan hukum yang berlaku. Anda dilarang keras untuk:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Menggunakan Layanan untuk aktivitas ilegal, penipuan, atau melanggar hak orang lain.</li>
                            <li>Mencoba merusak, memodifikasi, atau mengakses area server kami yang tidak bersifat publik.</li>
                            <li>Mengunggah berkas yang mengandung virus, malware, atau kode berbahaya lainnya.</li>
                        </ul>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold text-gray-950 dark:text-white">4. Kepemilikan Data</h2>
                        <p>Semua data klien, quotation, invoice, dan data keuangan lainnya yang Anda masukkan ke platform adalah milik Anda sepenuhnya. Paperwork tidak mengklaim kepemilikan atas konten yang Anda unggah. Anda memberikan izin kepada kami untuk memproses data tersebut semata-mata untuk menyediakan Layanan kepada Anda.</p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold text-gray-950 dark:text-white">5. Layanan Berlangganan & Pembayaran</h2>
                        <p>Beberapa fitur atau kapasitas tertentu di Paperwork memerlukan pembayaran paket berlangganan. Detail mengenai paket berlangganan, harga, dan metode pembayaran diatur di halaman tagihan. Semua biaya yang telah dibayarkan tidak dapat dikembalikan kecuali diwajibkan oleh hukum.</p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold text-gray-950 dark:text-white">6. Batasan Tanggung Jawab</h2>
                        <p>Paperwork disediakan "sebagaimana adanya" tanpa jaminan dalam bentuk apa pun. Kami tidak bertanggung jawab atas kerugian finansial, kehilangan data, atau gangguan bisnis yang disebabkan oleh penggunaan atau ketidakmampuan menggunakan Layanan kami.</p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold text-gray-950 dark:text-white">7. Pemutusan Akun</h2>
                        <p>Kami berhak untuk menangguhkan atau menghentikan akses Anda ke Layanan jika Anda terbukti melanggar Ketentuan Pelanggan ini. Anda juga berhak menghapus akun Anda kapan saja melalui pengaturan profil di platform.</p>
                    </section>
                </div>
            </article>

            <footer class="mt-16 border-t border-[#EAEAEA] pt-6 text-center text-xs text-gray-500 dark:border-white/10 dark:text-gray-400">
                <p>&copy; {{ now()->year }} Paperwork. Seluruh hak cipta dilindungi.</p>
            </footer>
        </div>
    </main>
@endsection
