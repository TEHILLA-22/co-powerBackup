<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Minimum Order Value
    |--------------------------------------------------------------------------
    */
    'minimum_order_value' => env('MINIMUM_ORDER_VALUE', 2000),

    /*
    |--------------------------------------------------------------------------
    | Admin Notification Emails
    |--------------------------------------------------------------------------
    */
    'admin_notification_emails' => env('ADMIN_NOTIFICATION_EMAILS', 'info@coopower.co.uk,sales@copower.com'),

    /*
    |--------------------------------------------------------------------------
    | Default Customer Tier
    |--------------------------------------------------------------------------
    */
    'default_tier_id' => env('DEFAULT_CUSTOMER_TIER', 1),

    /*
    |--------------------------------------------------------------------------
    | Registration Settings
    |--------------------------------------------------------------------------
    */
    'registration' => [
        'require_vat' => env('REQUIRE_VAT', false),
        'require_registration_number' => env('REQUIRE_REGISTRATION', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | OTP Settings
    |--------------------------------------------------------------------------
    */
    'otp' => [
        'expiry_minutes' => env('OTP_EXPIRY_MINUTES', 10),
        'max_attempts' => env('OTP_MAX_ATTEMPTS', 5),
        'max_resend_attempts' => env('OTP_MAX_RESEND_ATTEMPTS', 5),
    ],
];