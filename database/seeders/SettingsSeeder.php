<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'Copower Wholesale',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Site name displayed in header and titles',
                'is_public' => true,
            ],
            [
                'key' => 'site_logo',
                'value' => '/images/logo.png',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Site logo path',
                'is_public' => true,
            ],
            [
                'key' => 'site_favicon',
                'value' => '/images/favicon.ico',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Site favicon path',
                'is_public' => true,
            ],

            // Catalog Settings
            [
                'key' => 'products_per_page',
                'value' => '24',
                'group' => 'catalog',
                'type' => 'integer',
                'description' => 'Number of products shown per page',
                'is_public' => false,
            ],
            [
                'key' => 'enable_quick_order',
                'value' => 'true',
                'group' => 'catalog',
                'type' => 'boolean',
                'description' => 'Enable quick order by SKU/EAN',
                'is_public' => false,
            ],
            [
                'key' => 'show_out_of_stock',
                'value' => 'false',
                'group' => 'catalog',
                'type' => 'boolean',
                'description' => 'Show out of stock products in catalog',
                'is_public' => false,
            ],
            [
                'key' => 'enable_wishlist',
                'value' => 'true',
                'group' => 'catalog',
                'type' => 'boolean',
                'description' => 'Enable wishlist functionality',
                'is_public' => false,
            ],
            [
                'key' => 'enable_bulk_order',
                'value' => 'true',
                'group' => 'catalog',
                'type' => 'boolean',
                'description' => 'Enable bulk order builder',
                'is_public' => false,
            ],

            // Quote Settings
            [
                'key' => 'minimum_order_value',
                'value' => '5000',
                'group' => 'quotes',
                'type' => 'integer',
                'description' => 'Minimum order value in GBP',
                'is_public' => false,
            ],
            [
                'key' => 'quote_valid_days',
                'value' => '7',
                'group' => 'quotes',
                'type' => 'integer',
                'description' => 'Number of days a quote is valid',
                'is_public' => false,
            ],
            [
                'key' => 'enable_auto_quote_approval',
                'value' => 'false',
                'group' => 'quotes',
                'type' => 'boolean',
                'description' => 'Auto approve quotes for verified customers',
                'is_public' => false,
            ],

            // Security Settings
            [
                'key' => 'max_login_attempts',
                'value' => '5',
                'group' => 'security',
                'type' => 'integer',
                'description' => 'Max login attempts before lockout',
                'is_public' => false,
            ],
            [
                'key' => 'lockout_duration_minutes',
                'value' => '30',
                'group' => 'security',
                'type' => 'integer',
                'description' => 'Account lockout duration in minutes',
                'is_public' => false,
            ],
            [
                'key' => 'enable_2fa',
                'value' => 'false',
                'group' => 'security',
                'type' => 'boolean',
                'description' => 'Enable two-factor authentication for admin',
                'is_public' => false,
            ],
            [
                'key' => 'session_timeout_minutes',
                'value' => '120',
                'group' => 'security',
                'type' => 'integer',
                'description' => 'Session timeout in minutes',
                'is_public' => false,
            ],

            // SMTP Settings (Encrypted)
            [
                'key' => 'smtp_host',
                'value' => 'smtp.gmail.com',
                'group' => 'email',
                'type' => 'string',
                'description' => 'SMTP host server',
                'is_public' => false,
                'is_encrypted' => false,
            ],
            [
                'key' => 'smtp_port',
                'value' => '587',
                'group' => 'email',
                'type' => 'integer',
                'description' => 'SMTP port',
                'is_public' => false,
                'is_encrypted' => false,
            ],
            [
                'key' => 'smtp_encryption',
                'value' => 'tls',
                'group' => 'email',
                'type' => 'string',
                'description' => 'SMTP encryption type',
                'is_public' => false,
                'is_encrypted' => false,
            ],
            [
                'key' => 'smtp_username',
                'value' => '',
                'group' => 'email',
                'type' => 'string',
                'description' => 'SMTP username',
                'is_public' => false,
                'is_encrypted' => false,
            ],
            [
                'key' => 'smtp_password',
                'value' => '',
                'group' => 'email',
                'type' => 'string',
                'description' => 'SMTP password',
                'is_public' => false,
                'is_encrypted' => true,
            ],
            [
                'key' => 'mail_from_address',
                'value' => 'admin@copower.com',
                'group' => 'email',
                'type' => 'string',
                'description' => 'From email address',
                'is_public' => false,
                'is_encrypted' => false,
            ],
            [
                'key' => 'mail_from_name',
                'value' => 'Copower Wholesale',
                'group' => 'email',
                'type' => 'string',
                'description' => 'From name',
                'is_public' => false,
                'is_encrypted' => false,
            ],
            [
                'key' => 'admin_notification_emails',
                'value' => 'admin@copower.com,sales@copower.com',
                'group' => 'email',
                'type' => 'string',
                'description' => 'Admin notification emails (comma separated)',
                'is_public' => false,
                'is_encrypted' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
