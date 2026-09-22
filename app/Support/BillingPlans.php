<?php

namespace App\Support;

use App\Models\SystemSetting;

class BillingPlans
{
    public static function all(): array
    {
        return [
            [
                'slug' => 'basic',
                'name' => 'Basic',
                'amount' => (int) SystemSetting::get('plan_price_basic', SystemSetting::get('plan_price_starter', 49000)),
                'features' => [
                    'Hingga 50 datas',
                    'Kelola hingga 50 klien & 50 produk',
                    'Buat hingga 50 invoice & penawaran/bulan',
                    'Unduh PDF dokumen tanpa watermark',
                    'Riwayat pembayaran manual & QRIS',
                ],
            ],
            [
                'slug' => 'plus',
                'name' => 'Plus',
                'amount' => (int) SystemSetting::get('plan_price_plus', SystemSetting::get('plan_price_business', 149000)),
                'features' => [
                    'Maksimal 200 datas',
                    'Kelola hingga 200 klien & 200 produk',
                    'Buat hingga 200 invoice & penawaran/bulan',
                    'Pembayaran bertahap dan catatan termin',
                    'Riwayat pembayaran dan integrasi QRIS',
                    'Pengaturan rekening bank dan profil perusahaan',
                    'Unduh PDF dokumen tanpa watermark',
                ],
            ],
            [
                'slug' => 'enterprise',
                'name' => 'Enterprise',
                'amount' => (int) SystemSetting::get('plan_price_enterprise', 199000),
                'features' => [
                    'Unlimited datas (klien, produk, & dokumen)',
                    'Buat invoice & penawaran tanpa batas',
                    'Semua fitur paket Plus',
                    'Prioritas dukungan operasional',
                    'Pendampingan setup dokumen perusahaan',
                    'Review konfigurasi billing khusus',
                    'Unduh PDF dokumen tanpa watermark',
                ],
            ],
        ];
    }

    public static function find(string $slug): ?array
    {
        $aliasMap = [
            'starter' => 'basic',
            'business' => 'plus',
        ];
        $targetSlug = $aliasMap[$slug] ?? $slug;

        return collect(self::all())->firstWhere('slug', $targetSlug);
    }

    public static function amountFor(array $plan, string $period): int
    {
        return $period === 'yearly'
            ? (int) round($plan['amount'] * 12 * 0.9)
            : $plan['amount'];
    }
}
