<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'app.name',            'value' => 'ArenaBook',        'type' => 'string',  'group' => 'general'],
            ['key' => 'app.timezone',         'value' => 'Asia/Dhaka',       'type' => 'string',  'group' => 'general'],
            ['key' => 'app.currency',         'value' => 'BDT',              'type' => 'string',  'group' => 'general'],
            ['key' => 'app.currency_symbol',  'value' => '৳',               'type' => 'string',  'group' => 'general'],
            ['key' => 'app.address',          'value' => 'Dhaka, Bangladesh','type' => 'string',  'group' => 'general'],
            ['key' => 'app.phone',            'value' => '',                 'type' => 'string',  'group' => 'general'],
            ['key' => 'app.email',            'value' => '',                 'type' => 'string',  'group' => 'general'],

            // Booking rules
            ['key' => 'booking.min_advance_hours',         'value' => '2',    'type' => 'integer', 'group' => 'booking'],
            ['key' => 'booking.max_advance_days',          'value' => '60',   'type' => 'integer', 'group' => 'booking'],
            ['key' => 'booking.auto_confirm',              'value' => 'false', 'type' => 'boolean', 'group' => 'booking'],
            ['key' => 'booking.allow_same_day',            'value' => 'true', 'type' => 'boolean', 'group' => 'booking'],
            ['key' => 'booking.cancellation_notice_hours', 'value' => '24',   'type' => 'integer', 'group' => 'booking'],

            // Notifications
            ['key' => 'notification.email_enabled',       'value' => 'true', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'notification.sms_enabled',         'value' => 'false','type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'notification.reminder_hours_before','value' => '24',  'type' => 'integer', 'group' => 'notifications'],

            // SMS
            ['key' => 'sms.gateway', 'value' => 'ssl_wireless', 'type' => 'string', 'group' => 'sms'],
            ['key' => 'sms.user',    'value' => '',              'type' => 'string', 'group' => 'sms'],
            ['key' => 'sms.pass',    'value' => '',              'type' => 'string', 'group' => 'sms'],
            ['key' => 'sms.sid',     'value' => '',              'type' => 'string', 'group' => 'sms'],
            ['key' => 'sms.msisdn',  'value' => '',              'type' => 'string', 'group' => 'sms'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
