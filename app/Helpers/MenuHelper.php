<?php

namespace App\Helpers;

class MenuHelper
{
    public static function getMainNavItems()
    {
        return [
            [
                'icon' => 'dashboard',
                'name' => 'Dashboard',
                'path' => '/',
            ],
            [
                'icon' => 'user-profile',
                'name' => 'Clients',
                'path' => '/clients',
            ],
            [
                'icon' => 'ecommerce',
                'name' => 'Products',
                'path' => '/products',
            ],
            [
                'name' => 'Documents',
                'icon' => 'forms',
                'subItems' => [
                    ['name' => 'Quotations', 'path' => '/quotations', 'pro' => false],
                    ['name' => 'Invoices', 'path' => '/invoices', 'pro' => false],
                ],
            ],
            [
                'name' => 'Settings',
                'icon' => 'pages',
                'subItems' => [
                    ['name' => 'Company Profile', 'path' => '/settings/company', 'pro' => false],
                    ['name' => 'Billing', 'path' => '/settings/billing', 'pro' => false],
                    ['name' => 'Bank Accounts', 'path' => '/settings/bank-accounts', 'pro' => false],
                    ['name' => 'Security', 'path' => '/settings/security', 'pro' => false],
                ],
            ],
            [
                'icon' => 'email',
                'name' => 'Notifications',
                'path' => '/notifications',
            ],
        ];
    }

    public static function getOthersItems()
    {
        $items = [
            [
                'icon' => 'authentication',
                'name' => 'Auth',
                'subItems' => [
                    ['name' => 'Login', 'path' => '/login', 'pro' => false],
                    ['name' => 'Register', 'path' => '/register', 'pro' => false],
                ],
            ],
        ];

        if (auth()->user()?->isSuperAdmin()) {
            $items[] = [
                'icon' => 'support-ticket',
                'name' => 'Super Admin',
                'path' => '/super-admin',
            ];
        }

        return $items;
    }

    public static function getMenuGroups()
    {
        return [
            [
                'title' => 'Menu',
                'items' => self::getMainNavItems()
            ],
        ];
    }

    public static function isActive($path)
    {
        return request()->is(ltrim($path, '/'));
    }

    public static function getIconSvg($iconName)
    {
        $icons = [
            'dashboard' => '<i class="bx bx-grid-alt text-xl"></i>',
            'ai-assistant' => '<i class="bx bx-bot text-xl"></i>',
            'ecommerce' => '<i class="bx bx-shopping-bag text-xl"></i>',
            'calendar' => '<i class="bx bx-calendar text-xl"></i>',
            'user-profile' => '<i class="bx bx-user text-xl"></i>',
            'task' => '<i class="bx bx-task text-xl"></i>',
            'forms' => '<i class="bx bx-file text-xl"></i>',
            'tables' => '<i class="bx bx-table text-xl"></i>',
            'pages' => '<i class="bx bx-cog text-xl"></i>',
            'charts' => '<i class="bx bx-bar-chart-alt-2 text-xl"></i>',
            'ui-elements' => '<i class="bx bx-cube text-xl"></i>',
            'authentication' => '<i class="bx bx-lock-alt text-xl"></i>',
            'chat' => '<i class="bx bx-chat text-xl"></i>',
            'support-ticket' => '<i class="bx bx-support text-xl"></i>',
            'email' => '<i class="bx bx-envelope text-xl"></i>',
        ];

        return $icons[$iconName] ?? '<i class="bx bx-help-circle text-xl"></i>';
    }
}
