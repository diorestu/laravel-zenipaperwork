# 📄 Zeni Paperwork SaaS — Platform Otomasi Invoice, Expense & Laporan Keuangan

![Zeni Paperwork SaaS](marketing-assets/hardsell_poster.jpg)

**Zeni Paperwork** adalah platform SaaS *Billing, Invoicing, Expense Management*, dan *Financial Reporting* modern berbasis **Laravel 12**, **Tailwind CSS v4**, dan **Alpine.js**. Dirancang khusus untuk membantu UMKM, Agensi Kreatif, Software House, Konsultan, Toko Online, dan Freelancer di Indonesia dalam mengelola tagihan, memantau beban operasional, dan membuat laporan keuangan serta pajak secara otomatis.

---

## 🔗 Quick Links & Akses Sistem

- 🌐 **Website Resmi:** [https://paperwork.biz.id](https://paperwork.biz.id)
- 📚 **Dokumentasi API Interaktif (UI):** [https://paperwork.biz.id/docs/api](https://paperwork.biz.id/docs/api)
- 📱 **Mobile App Web Workspace:** [https://paperwork.biz.id/mobile](https://paperwork.biz.id/mobile)
- 🎨 **Marketing Kit & Product Knowledge:** [`PRODUCT_KNOWLEDGE_AND_POSTERS.md`](./PRODUCT_KNOWLEDGE_AND_POSTERS.md)

---

## ✨ Fitur Unggulan Sistem

![Fitur Utama Paperwork](marketing-assets/product_features_infographic.jpg)

### 1. ⚡ Invoice DP & Pelunasan (Parent-Child Linkage)
- Skema pembuatan **Invoice Down Payment (DP)** yang hanya menampilkan nilai DP tanpa total keseluruhan.
- Pembuatan **Invoice Pelunasan** secara otomatis dalam 1-klik dengan sisa saldo tagihan yang dihitung secara presisi.
- Kotak referensi otomatis pada tampilan web & PDF invoice.

### 2. 💸 Modul Catat Pengeluaran (Expense Management)
- Pencatatan pengeluaran bisnis per kategori (*Operasional, Gaji & Honor, Sewa & Utilitas, Marketing, Modal Proyek, Lain-lain*).
- Fitur unggah foto nota / kuitansi bukti transfer.
- Opsi menautkan pengeluaran langsung ke invoice proyek tertentu.
- Akses *Quick Access Expense* di **Mobile App Workspace**.

### 3. 📊 Laporan Keuangan & Rekap Pajak E-Faktur DJP
- **Arus Kas (*Cash Flow*):** Total kas masuk (*inflow*) & kas keluar (*outflow*).
- **Laba Rugi (*Profit & Loss*):** Pendapatan kotor, diskon, pendapatan bersih, total beban, PPh, dan Laba Bersih (*Net Profit*).
- **Umur Piutang (*Aging Accounts Receivable*):** Pengelompokan piutang `Current (0-30 Hari)`, `1-30 Hari Overdue`, `31-60 Hari Overdue`, `61-90 Hari Overdue`, dan `>90 Hari (Macet)`.
- **Rekap Pajak & Ekspor CSV:** Kalkulasi DPP, PPN Keluaran, PPh Pemotongan, dan ekspor CSV siap impor untuk SPT Masa Pajak DJP.
- **Ekspor PDF & CSV:** Cetak laporan formal untuk pemilik usaha dan investor.

### 4. 📱 Mobile App Workspace & PWA (`/mobile`)
- Antarmuka khusus pengguna smartphone yang ultra-ringan.
- **Pemuatan Bertahap (*Incremental Loading*):** Memuat 5 data awal dan tombol *Tampilkan Data Lainnya (+10)* secara *asynchronous* tanpa memberatkan server.
- Dukungan instalasi PWA (*Progressive Web App*).

### 5. 🛡 Keamanan & Manajemen Akses
- Enkripsi kata sandi `Bcrypt` dengan verifikasi `Hash::check`.
- Fitur pencabutan akses token perangkat (*Sanctum Personal Access Tokens*).
- Integrasi **Google Single Sign-On (SSO)**.
- Isolasi Data Perusahaan (*Company Scope Isolation*).

### 6. 🌐 Developer API & Multi Domain Docs
- RESTful API lengkap untuk integrasi dengan aplikasi pihak ketiga & aplikasi mobile.
- Dokumentasi interaktif OpenAPI di `/docs/api`.
- Dukungan domain khusus subdomain via `.env` (`DOCS_DOMAIN=docs.paperwork.biz.id`).

---

## 📊 Executive Overview

![Product Knowledge Executive Summary](marketing-assets/pk_onepager_graphic.jpg)

---

## 📋 Persyaratan Sistem

- **PHP:** `^8.2` (dengan ekstensi `pdo`, `mbstring`, `openssl`, `gd`/`imagick`)
- **Composer:** `^2.5`
- **Node.js & npm:** Node `^18.0` / npm `^9.0`
- **Database:** SQLite (default dev), MySQL `^8.0`, atau PostgreSQL `^15`

---

## 🚀 Panduan Instalasi Cepat

### 1. Clone Repositori & Install Dependencies

```bash
git clone https://github.com/diorestu/laravel-zenipaperwork.git paperwork-laravel
cd paperwork-laravel

# Install PHP Dependencies
composer install

# Install Frontend Dependencies
npm install
```

### 2. Konfigurasi Environment & Database

```bash
cp .env.example .env
php artisan key:generate
```

Ubah konfigurasi database pada `.env`:
```env
DB_CONNECTION=sqlite
# Atau MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=paperwork_db
# DB_USERNAME=root
# DB_PASSWORD=secret
```

### 3. Migrasi & Seed Database

```bash
php artisan migrate --seed
php artisan storage:link
```

### 4. Jalankan Server Dev

```bash
composer run dev
```
Atau secara terpisah:
```bash
# Terminal 1:
php artisan serve

# Terminal 2:
npm run dev
```

Akses aplikasi di browser: **[http://localhost:8000](http://localhost:8000)**

---

## 🧪 Pengujian Otomatis (Testing)

Aplikasi dilengkapi dengan suite pengujian otomatis berbasis **Pest / PHPUnit** (42 Feature & Unit Tests, 308 Assertions):

```bash
php artisan test
```

---

## 📁 Struktur Direktori Proyek Utama

```
paperwork-laravel/
├── app/
│   ├── Helpers/            # MenuHelper & global helpers
│   ├── Http/
│   │   ├── Controllers/    # InvoiceController, ReportController, ExpenseWebController, dll.
│   │   └── Requests/       # Form Request Validation
│   ├── Models/             # Model Eloquent (Invoice, Expense, Client, Product, Company, dll.)
│   └── Services/           # FinancialReportService, InvoiceService
├── database/
│   └── migrations/         # Struktur tabel database
├── marketing-assets/       # Visual poster & infografis produk
├── resources/
│   ├── views/
│   │   ├── components/     # Component UI Blade & status badges
│   │   ├── expenses/       # Halaman Manajemen Pengeluaran
│   │   ├── invoices/       # Form & Preview Invoice (DP & Pelunasan)
│   │   ├── mobile/         # Mobile Workspace App UI
│   │   ├── pdf/            # Template Cetak PDF Invoice & Laporan Keuangan
│   │   ├── reports/        # Halaman Laporan Keuangan & Pajak
│   │   └── settings/       # Halaman Pengaturan (Keamanan, Perusahaan, Rekening, Data)
├── routes/
│   ├── api.php             # REST API Mobile & Integrasi
│   └── web.php             # Route Web Application & Subdomain Docs
├── PRODUCT_KNOWLEDGE_AND_POSTERS.md # Panduan Marketing & Product Knowledge
└── README.md
```

---

## 📄 Lisensi

Hak Cipta © 2026 **Zeni Paperwork**. Seluruh hak dilindungi undang-undang.
