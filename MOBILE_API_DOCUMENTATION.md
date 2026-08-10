# 📱 Dokumentasi Mobile API - Paperwork

Dokumentasi API ini ditujukan untuk pengembang aplikasi Mobile (iOS/Android) yang mengintegrasikan layanan **Paperwork**.

---

## 🌐 Baseline & Autentikasi

- **Base URL Production:** `https://paperwork.biz.id/api/v1`
- **Base URL Local/Dev:** `http://localhost:8000/api/v1`
- **Interactive OpenAPI UI:** `https://paperwork.biz.id/docs/api`
- **OpenAPI 3.0 Spec File:** `paperwork-api-openapi.json` (Berada di root project)

### Dynamic Headers
Setiap request yang memerlukan autentikasi **wajib** menyertakan header berikut:

```http
Accept: application/json
Content-Type: application/json
Authorization: Bearer <YOUR_SANCTUM_TOKEN>
```

---

## 🔑 1. Autentikasi & Akun (`/api/v1`)

### `POST /register`
Mendaftarkan pengguna dan perusahaan baru.
- **Request Body:**
  ```json
  {
    "name": "Budi Santoso",
    "company_name": "PT Maju Bersama",
    "email": "budi@majubersama.com",
    "password": "Password123!",
    "password_confirmation": "Password123!"
  }
  ```
- **Response (201 Created):**
  ```json
  {
    "token": "1|sanctum_token_string...",
    "user": {
      "id": 1,
      "name": "Budi Santoso",
      "email": "budi@majubersama.com",
      "company": {
        "id": 1,
        "name": "PT Maju Bersama",
        "active_plan": "free"
      }
    }
  }
  ```

### `POST /login`
Login menggunakan email dan password.
- **Request Body:**
  ```json
  {
    "email": "budi@majubersama.com",
    "password": "Password123!"
  }
  ```

### `GET /me`
Mengambil informasi pengguna & perusahaan yang sedang login.

### `POST /logout`
Mencabut token autentikasi saat ini.

---

## 📊 2. Dashboard (`/api/v1/dashboard`)

### `GET /dashboard`
Mengambil ringkasan metrik dashboard keuangan & invoice.
- **Response:**
  ```json
  {
    "metrics": {
      "total_invoices_count": 12,
      "total_paid_amount": 15000000.0,
      "total_unpaid_amount": 5000000.0,
      "total_overdue_amount": 1200000.0
    },
    "recent_invoices": [...]
  }
  ```

---

## 🧾 3. Invoices (`/api/v1/invoices`)

### `GET /invoices`
Mengambil daftar invoice dengan dukungan pagination & pencarian.
- **Query Parameters:**
  - `status` (opsional): `draft`, `sent`, `partial`, `paid`, `void`
  - `payment_status` (opsional): `unpaid`, `paid`, `overdue`
  - `search` (opsional): Kata kunci nomor invoice atau nama klien
  - `per_page` (opsional): Jumlah item per halaman (default: 15)

### `POST /invoices`
Membuat invoice baru dengan dukungan **Custom Taxes**, **Diskon (Opsional)**, dan **Split Payment (Termin)**.
- **Request Body:**
  ```json
  {
    "client_id": 1,
    "number": "INV/2026/08/0001",
    "issue_date": "2026-08-10",
    "due_date": "2026-08-24",
    "status": "sent",
    "discount_type": "percentage",
    "discount_rate": 10,
    "discount_amount": 0,
    "custom_taxes": [
      {
        "name": "PPN",
        "rate": 11,
        "type": "addition"
      },
      {
        "name": "PPh 23",
        "rate": 2,
        "type": "deduction"
      }
    ],
    "items": [
      {
        "product_id": 2,
        "description": "Pengembangan Fitur Aplikasi Mobile",
        "quantity": 1,
        "unit_price": 5000000
      }
    ],
    "payment_terms": [
      {
        "label": "DP 50%",
        "amount": 2500000,
        "due_date": "2026-08-10"
      },
      {
        "label": "Pelunasan",
        "amount": 2405000,
        "due_date": "2026-08-24"
      }
    ],
    "notes": "Terima kasih atas kerja sama Anda."
  }
  ```

### `GET /invoices/{id}`
Mengambil rincian detail 1 invoice beserta items, pembayaran, termin, dan custom taxes.

### `PUT /invoices/{id}`
Memperbarui data invoice.

### `DELETE /invoices/{id}`
Menghapus invoice.

### `POST /invoices/{id}/payments`
Mencatat pembayaran masuk (termasuk upload bukti bayar multipart/form-data).
- **Content-Type:** `multipart/form-data`
- **Form Fields:**
  - `payment_date` (required, date `YYYY-MM-DD`)
  - `amount` (required, numeric)
  - `method` (required: `bank_transfer`, `cash`, `qris`, `credit_card`, `other`)
  - `reference` (optional, string)
  - `term_number` (optional, integer)
  - `notes` (optional, string)
  - `proof` (optional, file image/pdf max 5MB)

### `POST /invoices/{id}/send`
Mengirimkan invoice ke email klien.

### `GET /invoices/{id}/pdf`
Mengunduh file PDF Invoice.

---

## 📄 4. Penawaran / Quotation (`/api/v1/quotations`)

### `GET /quotations`
Mengambil daftar penawaran harga.

### `POST /quotations`
Membuat penawaran harga baru (mendukung **Custom Taxes**, **Diskon**, dan **Termin Pembayaran**).
- **Request Body:**
  ```json
  {
    "client_id": 1,
    "number": "QUO/2026/08/0001",
    "issue_date": "2026-08-10",
    "valid_until": "2026-08-24",
    "status": "sent",
    "discount_type": "fixed",
    "discount_amount": 100000,
    "custom_taxes": [
      {
        "name": "PPN",
        "rate": 11,
        "type": "addition"
      }
    ],
    "items": [
      {
        "description": "Desain UI/UX Mobile App",
        "quantity": 1,
        "unit_price": 2000000
      }
    ]
  }
  ```

### `POST /quotations/{id}/convert`
Mengonversi penawaran yang telah disetujui menjadi Invoice secara otomatis.

---

## 👥 5. Klien & 📦 Produk (`/api/v1`)

- `GET /clients` | `POST /clients` | `GET /clients/{id}` | `PUT /clients/{id}` | `DELETE /clients/{id}`
- `GET /products` | `POST /products` | `GET /products/{id}` | `PUT /products/{id}` | `DELETE /products/{id}`

---

## 🏦 6. Rekening Bank (`/api/v1/bank-accounts`)

- `GET /bank-accounts`
- `POST /bank-accounts`
  ```json
  {
    "bank_name": "BCA",
    "account_name": "PT Maju Bersama",
    "account_number": "1234567890",
    "is_active": true
  }
  ```

---

## 🔔 7. Notifikasi & Push Tokens (`/api/v1`)

### `GET /notifications`
Mengambil daftar notifikasi terbaru.

### `POST /notifications/read-all`
Tandai semua notifikasi telah dibaca.

### `POST /device-tokens`
Daftarkan token FCM/APNS perangkat mobile untuk Push Notification.
```json
{
  "token": "fcm_token_string...",
  "device_type": "android"
}
```

### `DELETE /device-tokens`
Hapus token push notification perangkat.

---

## 💳 8. Billing & Langganan Paket (`/api/v1/billing`)

- `GET /billing/plans` - Mengambil daftar paket langganan (Starter, Business, Enterprise)
- `GET /billing/submissions` - Daftar riwayat bukti pembayaran langganan
- `POST /billing/submissions` - Upload bukti konfirmasi pembayaran langganan manual

---

## 🧪 Pengujian Automated & Swagger Spec
File rincian OpenAPI JSON lengkap dapat diakses secara publik di server:
`https://paperwork.biz.id/docs/api` atau diunduh dari file local [`paperwork-api-openapi.json`](file:///Users/user/Documents/PROJECTS/paperwork-laravel/paperwork-api-openapi.json).
