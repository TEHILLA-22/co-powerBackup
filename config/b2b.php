<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Notification Emails
    |--------------------------------------------------------------------------
    | These emails will receive notifications when a new customer registers.
    */
    'admin_notification_emails' => [
        'admin@copower.com',
        'sales@copower.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Minimum Order Value
    |--------------------------------------------------------------------------
    */
    'minimum_order_value' => env('MINIMUM_ORDER_VALUE', 5000),

    /*
    |--------------------------------------------------------------------------
    | Default Customer Tier
    |--------------------------------------------------------------------------
    */
    'default_tier_id' => env('DEFAULT_CUSTOMER_TIER', 1),

    /*
    |--------------------------------------------------------------------------
    | Registration
    |--------------------------------------------------------------------------
    */
    'registration' => [
        'require_vat' => env('REQUIRE_VAT', false),
        'require_registration_number' => env('REQUIRE_REGISTRATION', false),
    ],
];