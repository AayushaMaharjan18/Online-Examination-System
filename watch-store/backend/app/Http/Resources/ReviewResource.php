<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Resolve the moderation status. New rows use the `status` column, but
        // we also tolerate legacy rows that only ever set `is_approved`.
        $status = $this->status
            ?? ($this->is_approved ? 'approved' : 'pending');

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
            'order_id' => $this->order_id,
            'rating' => (int) $this->rating,
            'title' => $this->title,
            'comment' => $this->comment,
            'status' => $status,
            'is_approved' => $status === 'approved',
            'is_visible_on_homepage' => (bool) $this->is_visible_on_homepage,
            'is_verified_purchase' => (bool) $this->is_verified_purchase,
            'user' => $this->whenLoaded('user', function (): ?array {
                if (! $this->user) {
                    return null;
                }

                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'product' => $this->whenLoaded('product', function (): ?ProductResource {
                return $this->product ? new ProductResource($this->product) : null;
            }),
            'order' => $this->whenLoaded('order', function (): ?array {
                if (! $this->order) {
                    return null;
                }

                return [
                    'id' => $this->order->id,
                    'order_number' => $this->order->order_number,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
