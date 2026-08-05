<?php

namespace App\Support;

class BillingPlans
{
    public static function all(): array
    {
        return [
            [
                'slug' => 'starter',
                'name' => 'Starter',
                'amount' => 25000,
                'features' => [
                    'Kelola hingga 100 klien',
                    'Kelola hingga 100 produk atau layanan',
                    'Buat hingga 50 invoice/quotation per bulan',
                    'Unduh PDF invoice dan penawaran',
                    'Riwayat pembayaran manual',
                ],
            ],
            [
                'slug' => 'business',
                'name' => 'Business',
                'amount' => 99000,
                'features' => [
                    'Kelola hingga 500 klien',
                    'Kelola hingga 500 produk atau layanan',
                    'Buat penawaran tanpa batas',
                    'Buat hingga 500 invoice',
                    'Pembayaran bertahap dan catatan termin',
                    'Riwayat pembayaran dan status pelunasan',
                    'Pengaturan rekening bank dan profil perusahaan',
                ],
            ],
            [
                'slug' => 'enterprise',
                'name' => 'Enterprise',
                'amount' => 299000,
                'features' => [
                    'Klien, produk, penawaran, dan invoice tanpa batas',
                    'Semua fitur Business',
                    'Prioritas dukungan operasional',
                    'Pendampingan setup dokumen perusahaan',
                    'Kebutuhan kapasitas dan workflow khusus',
                    'Review konfigurasi billing dan pembayaran',
                ],
            ],
        ];
    }

    public static function find(string $slug): ?array
    {
        return collect(self::all())->firstWhere('slug', $slug);
    }

    public static function amountFor(array $plan, string $period): int
    {
        return $period === 'yearly'
            ? (int) round($plan['amount'] * 12 * 0.9)
            : $plan['amount'];
    }
}
