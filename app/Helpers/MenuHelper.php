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
            'dashboard' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 5.5A1.5 1.5 0 0 1 5.5 4h4A1.5 1.5 0 0 1 11 5.5v4A1.5 1.5 0 0 1 9.5 11h-4A1.5 1.5 0 0 1 4 9.5v-4ZM13 5.5A1.5 1.5 0 0 1 14.5 4h4A1.5 1.5 0 0 1 20 5.5v4a1.5 1.5 0 0 1-1.5 1.5h-4A1.5 1.5 0 0 1 13 9.5v-4ZM4 14.5A1.5 1.5 0 0 1 5.5 13h4a1.5 1.5 0 0 1 1.5 1.5v4A1.5 1.5 0 0 1 9.5 20h-4A1.5 1.5 0 0 1 4 18.5v-4ZM13 14.5a1.5 1.5 0 0 1 1.5-1.5h4a1.5 1.5 0 0 1 1.5 1.5v4a1.5 1.5 0 0 1-1.5 1.5h-4a1.5 1.5 0 0 1-1.5-1.5v-4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'ai-assistant' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5V3m0 2c-3.866 0-7 2.686-7 6v4.5A2.5 2.5 0 0 0 7.5 18h9a2.5 2.5 0 0 0 2.5-2.5V11c0-3.314-3.134-6-7-6ZM9 12h.01M15 12h.01M9.5 16h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'ecommerce' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 8h10l-.65 10.1A2 2 0 0 1 14.35 20h-4.7a2 2 0 0 1-2-1.9L7 8Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 8a3 3 0 0 1 6 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'calendar' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3v3m10-3v3M5 9h14M6.5 5h11A2.5 2.5 0 0 1 20 7.5v10A2.5 2.5 0 0 1 17.5 20h-11A2.5 2.5 0 0 1 4 17.5v-10A2.5 2.5 0 0 1 6.5 5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'user-profile' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM5 20a7 7 0 0 1 14 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'task' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 6h11M9 12h11M9 18h11M4 6l1 1 2-2M4 12l1 1 2-2M4 18l1 1 2-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'forms' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3h7l4 4v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M14 3v5h4M9 12h6M9 16h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'tables' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v11a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 17.5v-11ZM4 10h16M10 4v16" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'pages' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" stroke="currentColor" stroke-width="1.8"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6V20a2 2 0 1 1-4 0v-.09a1.7 1.7 0 0 0-1-.6 1.7 1.7 0 0 0-1.88.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1H4a2 2 0 1 1 0-4h.09a1.7 1.7 0 0 0 .6-1 1.7 1.7 0 0 0-.34-1.88l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6V4a2 2 0 1 1 4 0v.09a1.7 1.7 0 0 0 1 .6 1.7 1.7 0 0 0 1.88-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9c.19.35.4.67.6 1H20a2 2 0 1 1 0 4h-.09c-.2.33-.41.65-.6 1Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'charts' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 19V5m5 14v-8m5 8V8m5 11V4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'ui-elements' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m4.5 8 7.5 4.2L19.5 8M12 12.2V21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'authentication' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 11V8a5 5 0 0 1 10 0v3M6.5 11h11A1.5 1.5 0 0 1 19 12.5v6A1.5 1.5 0 0 1 17.5 20h-11A1.5 1.5 0 0 1 5 18.5v-6A1.5 1.5 0 0 1 6.5 11Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'chat' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 18.5V7a3 3 0 0 1 3-3h8a3 3 0 0 1 3 3v5a3 3 0 0 1-3 3H9l-4 3.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'support-ticket' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 7.5A2.5 2.5 0 0 1 7.5 5h9A2.5 2.5 0 0 1 19 7.5V10a2 2 0 1 0 0 4v2.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 5 16.5V14a2 2 0 1 0 0-4V7.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 8v8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-dasharray="1 3"/></svg>',
            'email' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5.5 6h13A1.5 1.5 0 0 1 20 7.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 4 16.5v-9A1.5 1.5 0 0 1 5.5 6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m5 7 7 6 7-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ];

        return $icons[$iconName] ?? '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9.1 9a3 3 0 1 1 5.2 2c-.9.82-1.3 1.25-1.3 2.5M12 17h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    }
}
