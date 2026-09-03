<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ShippingDistrict;
use Illuminate\Http\JsonResponse;

class ShippingController extends Controller
{
    public function districts(): JsonResponse
    {
        $districts = ShippingDistrict::all(['id', 'name', 'cost', 'delivery_days']);

        return response()->json([
            'data' => $districts
        ]);
    }
}