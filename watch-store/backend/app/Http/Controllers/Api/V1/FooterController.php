<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FooterSetting;
use Illuminate\Http\JsonResponse;

class FooterController extends Controller
{
    public function index(): JsonResponse
    {
        $footer = FooterSetting::where('is_active', true)->first();

        if (!$footer) {
            return response()->json([
                'brand_name' => 'WATCHSTORE',
                'brand_description' => 'Your premier destination for luxury watches in Nepal.',
                'facebook_url' => null,
                'instagram_url' => null,
                'twitter_url' => null,
                'quick_links' => [],
                'customer_service_links' => [],
                'phone' => null,
                'email' => null,
                'address' => null,
                'copyright_text' => null,
                'payment_methods' => [],
                'whatsapp_number' => null,
            ]);
        }

        return response()->json([
            'brand_name' => $footer->brand_name,
            'brand_description' => $footer->brand_description,
            'facebook_url' => $footer->facebook_url,
            'instagram_url' => $footer->instagram_url,
            'twitter_url' => $footer->twitter_url,
            'quick_links' => $footer->quick_links ?? [],
            'customer_service_links' => $footer->customer_service_links ?? [],
            'phone' => $footer->phone,
            'email' => $footer->email,
            'address' => $footer->address,
            'copyright_text' => $footer->copyright_text,
            'payment_methods' => $footer->payment_methods ?? [],
            'whatsapp_number' => $footer->whatsapp_number,
        ]);
    }
}