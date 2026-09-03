<?php

return [

    'esewa' => [
        // Merchant ID (Service Code). Use the eSewa test code in development.
        'merchant_id' => env('ESEWA_MERCHANT_ID', 'EPAYTEST'),
        // eSewa payment initiation gateway.
        'gateway_url' => env('ESEWA_GATEWAY_URL', 'https://uat.esewa.com.np/epay/main'),
        // eSewa transaction lookup/verification gateway.
        'verify_url' => env('ESEWA_VERIFY_URL', 'https://uat.esewa.com.np/epay/transrec'),
    ],

    'khalti' => [
        // Khalti public key (used by the client-side checkout widget).
        'public_key' => env('KHALTI_PUBLIC_KEY', 'test_public_key_dc74e0fd57cb46cd93832aee0a507256'),
        // Khalti secret key (used server-side to verify a payment token).
        'secret_key' => env('KHALTI_SECRET_KEY', ''),
        // Khalti payment verification API.
        'verify_url' => env('KHALTI_VERIFY_URL', 'https://khalti.com/api/v2/payment/verify/'),
    ],

];