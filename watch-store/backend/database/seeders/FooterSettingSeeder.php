<?php

namespace Database\Seeders;

use App\Models\FooterSetting;
use Illuminate\Database\Seeder;

class FooterSettingSeeder extends Seeder
{
    public function run(): void
    {
        FooterSetting::firstOrCreate(
            ['id' => 1],
            [
                'brand_name' => 'WATCHSTORE',
                'brand_description' => 'Your premier destination for luxury watches in Nepal. Authentic timepieces from world-renowned brands.',
                'facebook_url' => 'https://facebook.com/watchstore',
                'instagram_url' => 'https://instagram.com/watchstore',
                'twitter_url' => 'https://twitter.com/watchstore',
                'quick_links' => [
                    ['label' => 'Shop All', 'url' => '/shop'],
                    ['label' => 'Brands', 'url' => '/brands'],
                    ['label' => 'Blog', 'url' => '/blog'],
                    ['label' => 'About Us', 'url' => '/about'],
                    ['label' => 'Contact', 'url' => '/contact'],
                ],
                'customer_service_links' => [
                    ['label' => 'Customer Service', 'url' => '/customer-service'],
                    ['label' => 'FAQ', 'url' => '/faq'],
                    ['label' => 'Shipping Info', 'url' => '/shipping'],
                    ['label' => 'Returns & Exchanges', 'url' => '/returns'],
                    ['label' => 'Privacy Policy', 'url' => '/privacy-policy'],
                    ['label' => 'Terms & Conditions', 'url' => '/terms'],
                ],
                'phone' => '+977-1-4XXXXXX',
                'whatsapp_number' => '+977-9800000000',
                'email' => 'info@watchstore.com.np',
                'address' => 'Kathmandu, Nepal',
                'copyright_text' => 'WatchStore Nepal. All rights reserved.',
                'payment_methods' => [
                    ['name' => 'eSewa'],
                    ['name' => 'Khalti'],
                    ['name' => 'COD'],
                ],
                'is_active' => true,
            ]
        );
    }
}