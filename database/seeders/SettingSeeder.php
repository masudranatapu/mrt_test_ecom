<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Default store / contact information. Existing values are kept.
     */
    public function run(): void
    {
        $defaults = [
            'store_name' => config('app.name'),
            'contact_phone' => '+880 1700-000000',
            'contact_email' => 'support@example.com',
            'contact_whatsapp' => '+8801700000000',
            'contact_address' => 'House 12, Road 5, Mirpur 10, Dhaka 1216, Bangladesh',
            'business_hours' => 'Sat - Thu, 9:00 AM - 8:00 PM',
            'map_location' => 'Mirpur 10, Dhaka, Bangladesh',
            'social_facebook' => 'https://facebook.com',
            'social_instagram' => 'https://instagram.com',
            'social_youtube' => 'https://youtube.com',
            'social_linkedin' => null,
        ];

        $existing = Setting::pluck('key')->all();

        Setting::setMany(array_diff_key($defaults, array_flip($existing)));
    }
}
