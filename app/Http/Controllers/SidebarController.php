<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SidebarController extends Controller
{
    public function getMenuData()
    {
        $menuGroups = [
            [
                'title' => 'Menu',
                'items' => [
                    [
                        'icon' => 'grid-icon',
                        'name' => 'Dasbor',
                        'subItems' => [
                            ['name' => 'E-commerce', 'path' => '/'],
                            ['name' => 'Analitik', 'path' => '/analytics'],
                            ['name' => 'Pemasaran', 'path' => '/marketing'],
                            ['name' => 'CRM', 'path' => '/crm'],
                            ['name' => 'Saham', 'path' => '/stocks'],
                            ['name' => 'SaaS', 'path' => '/saas', 'new' => true],
                            ['name' => 'Logistics', 'path' => '/logistics', 'new' => true],
                        ],
                    ],
                    [
                        'icon' => 'bot-icon',
                        'name' => 'Asisten AI',
                        'new' => true,
                        'subItems' => [
                            ['name' => 'Generator Teks', 'path' => '/text-generator'],
                            ['name' => 'Generator Gambar', 'path' => '/image-generator'],
                            ['name' => 'Generator Kode', 'path' => '/code-generator'],
                            ['name' => 'Generator Video', 'path' => '/video-generator'],
                        ],
                    ],
                    [
                        'icon' => 'cart-icon',
                        'name' => 'E-commerce',
                        'new' => true,
                        'subItems' => [
                            ['name' => 'Produk', 'path' => '/products-list'],
                            ['name' => 'Tambah Produk', 'path' => '/add-product'],
                            ['name' => 'Tagihan', 'path' => '/billing'],
                            ['name' => 'Invoice', 'path' => '/invoices'],
                            ['name' => 'Detail Invoice', 'path' => '/single-invoice'],
                            ['name' => 'Buat Invoice', 'path' => '/create-invoice'],
                            ['name' => 'Transaksi', 'path' => '/transactions'],
                            ['name' => 'Detail Transaksi', 'path' => '/single-transaction'],
                        ],
                    ],
                    [
                        'icon' => 'calendar-icon',
                        'name' => 'Kalender',
                        'path' => '/calendar',
                    ],
                    [
                        'icon' => 'user-circle-icon',
                        'name' => 'Profil Pengguna',
                        'path' => '/profile',
                    ],
                    [
                        'icon' => 'task-icon',
                        'name' => 'Tugas',
                        'subItems' => [
                            ['name' => 'Daftar', 'path' => '/task-list', 'pro' => false],
                            ['name' => 'Kanban', 'path' => '/task-kanban', 'pro' => false],
                        ],
                    ],
                    [
                        'icon' => 'list-icon',
                        'name' => 'Formulir',
                        'subItems' => [
                            ['name' => 'Elemen Formulir', 'path' => '/form-elements', 'pro' => false],
                            ['name' => 'Layout Formulir', 'path' => '/form-layout', 'pro' => false],
                        ],
                    ],
                    [
                        'icon' => 'table-icon',
                        'name' => 'Tabel',
                        'subItems' => [
                            ['name' => 'Tabel Dasar', 'path' => '/basic-tables', 'pro' => false],
                            ['name' => 'Tabel Data', 'path' => '/data-tables', 'pro' => false],
                        ],
                    ],
                    [
                        'icon' => 'page-icon',
                        'name' => 'Halaman',
                        'subItems' => [
                            ['name' => 'Pengelola File', 'path' => '/file-manager', 'pro' => false],
                            ['name' => 'Tabel Harga', 'path' => '/pricing-tables', 'pro' => false],
                            ['name' => 'FAQ', 'path' => '/faq', 'pro' => false],
                            ['name' => 'Kunci API', 'path' => '/api-keys', 'new' => true],
                            ['name' => 'Integrasi', 'path' => '/integrations', 'new' => true],
                            ['name' => 'Halaman Kosong', 'path' => '/blank', 'pro' => false],
                            ['name' => 'Error 404', 'path' => '/error-404', 'pro' => false],
                            ['name' => 'Error 500', 'path' => '/error-500', 'pro' => false],
                            ['name' => 'Error 503', 'path' => '/error-503', 'pro' => false],
                            ['name' => 'Segera Hadir', 'path' => '/coming-soon', 'pro' => false],
                            ['name' => 'Pemeliharaan', 'path' => '/maintenance', 'pro' => false],
                            ['name' => 'Berhasil', 'path' => '/success', 'pro' => false],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Dukungan',
                'items' => [
                    [
                        'icon' => 'chat-icon',
                        'name' => 'Chat',
                        'path' => '/chat',
                    ],
                    [
                        'icon' => 'call-icon',
                        'name' => 'Tiket Dukungan',
                        'new' => true,
                        'subItems' => [
                            ['name' => 'Daftar Tiket', 'path' => '/support-tickets'],
                            ['name' => 'Balasan Tiket', 'path' => '/support-ticket-reply'],
                        ],
                    ],
                    [
                        'icon' => 'mail-icon',
                        'name' => 'Email',
                        'subItems' => [
                            ['name' => 'Inbox', 'path' => '/inbox', 'pro' => false],
                            ['name' => 'Detail', 'path' => '/inbox-details', 'pro' => false],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Lainnya',
                'items' => [
                    [
                        'icon' => 'pie-chart-icon',
                        'name' => 'Grafik',
                        'subItems' => [
                            ['name' => 'Grafik Garis', 'path' => '/line-chart', 'pro' => false],
                            ['name' => 'Grafik Batang', 'path' => '/bar-chart', 'pro' => false],
                            ['name' => 'Grafik Pai', 'path' => '/pie-chart', 'pro' => false],
                        ],
                    ],
                    [
                        'icon' => 'box-cube-icon',
                        'name' => 'Elemen UI',
                        'subItems' => [
                            ['name' => 'Peringatan', 'path' => '/alerts', 'pro' => false],
                            ['name' => 'Avatar', 'path' => '/avatars', 'pro' => false],
                            ['name' => 'Badge', 'path' => '/badge', 'pro' => false],
                            ['name' => 'Breadcrumb', 'path' => '/breadcrumb', 'pro' => false],
                            ['name' => 'Tombol', 'path' => '/buttons', 'pro' => false],
                            ['name' => 'Grup Tombol', 'path' => '/buttons-group', 'pro' => false],
                            ['name' => 'Kartu', 'path' => '/cards', 'pro' => false],
                            ['name' => 'Carousel', 'path' => '/carousel', 'pro' => false],
                            ['name' => 'Dropdown', 'path' => '/dropdowns', 'pro' => false],
                            ['name' => 'Gambar', 'path' => '/image', 'pro' => false],
                            ['name' => 'Link', 'path' => '/links', 'pro' => false],
                            ['name' => 'Daftar', 'path' => '/list', 'pro' => false],
                            ['name' => 'Modals', 'path' => '/modals', 'pro' => false],
                            ['name' => 'Notifikasi', 'path' => '/notifications', 'pro' => false],
                            ['name' => 'Pagination', 'path' => '/pagination', 'pro' => false],
                            ['name' => 'Popover', 'path' => '/popovers', 'pro' => false],
                            ['name' => 'Progress Bar', 'path' => '/progress-bar', 'pro' => false],
                            ['name' => 'Ribbons', 'path' => '/ribbons', 'pro' => false],
                            ['name' => 'Pemuat', 'path' => '/spinners', 'pro' => false],
                            ['name' => 'Tab', 'path' => '/tabs', 'pro' => false],
                            ['name' => 'Tooltip', 'path' => '/tooltips', 'pro' => false],
                            ['name' => 'Video', 'path' => '/videos', 'pro' => false],
                        ],
                    ],
                    [
                        'icon' => 'plug-in-icon',
                        'name' => 'Autentikasi',
                        'subItems' => [
                            ['name' => 'Masuk', 'path' => '/signin', 'pro' => false],
                            ['name' => 'Daftar', 'path' => '/signup', 'pro' => false],
                            ['name' => 'Reset Kata Sandi', 'path' => '/reset-password', 'pro' => false],
                            ['name' => 'Verifikasi Dua Langkah', 'path' => '/two-step-verification', 'pro' => false],
                        ],
                    ],
                ],
            ],
        ];

        return view('components.sidebar', compact('menuGroups'));
    }
}
