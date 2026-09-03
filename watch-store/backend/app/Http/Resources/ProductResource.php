<?php

namespace App\Http\Resources;

use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'sku' => $this->sku,
            'price' => (float) $this->price,
            'compare_price' => $this->compare_price ? (float) $this->compare_price : null,
            'final_price' => (float) $this->final_price,
            'discount_percentage' => (float) $this->discount_percentage,
            'stock_quantity' => $this->stock_quantity,
            'in_stock' => (bool) $this->in_stock,
            'is_featured' => (bool) $this->is_featured,
            'is_new' => (bool) $this->is_new,
            'is_best_seller' => (bool) $this->is_best_seller,
            'is_limited_edition' => (bool) $this->is_limited_edition,
            'status' => $this->status,
            'gender' => $this->gender,
            'movement' => $this->movement,
            'strap' => $this->strap,
            'case_material' => $this->case_material,
            'case_diameter' => $this->case_diameter,
            'case_thickness' => $this->case_thickness,
            'water_resistance' => $this->water_resistance,
            'dial_color' => $this->dial_color,
            'glass_type' => $this->glass_type,
            'warranty_period' => $this->warranty_period,
            'average_rating' => (float) $this->average_rating,
            'reviews_count' => $this->reviews_count,
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'images' => MediaUrl::many($this->images),
            'thumbnail' => MediaUrl::for($this->thumbnail),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}