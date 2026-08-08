<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Prefix (Obscured Route)
    |--------------------------------------------------------------------------
    | This is the URL prefix for all admin routes. Change this to something
    | unique to obscure the admin panel from attackers.
    */
    'prefix' => env('ADMIN_PREFIX', 'copower/sales_admin1'),

    /*
    |--------------------------------------------------------------------------
    | Admin Roles
    |--------------------------------------------------------------------------
    */
    'roles' => [
        'super_admin' => [
            'permissions' => ['*'],
            'description' => 'Full access to everything',
        ],
        'admin' => [
            'permissions' => [
                'view_dashboard',
                'manage_products',
                'manage_orders',
                'manage_customers',
                'manage_settings',
            ],
            'description' => 'Standard admin access',
        ],
    ],
];