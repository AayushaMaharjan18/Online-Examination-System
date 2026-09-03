<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPlacementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_place_an_order_with_items(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Luxury Watch',
            'slug' => 'luxury-watch',
            'sku' => 'LW-001',
            'price' => 15000,
            'final_price' => 15000,
            'stock_quantity' => 10,
            'in_stock' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders', [
            'shipping_name' => 'John Doe',
            'shipping_phone' => '9812345678',
            'shipping_district' => 'Kathmandu',
            'shipping_municipality' => 'Budhanilkantha',
            'shipping_street' => 'Main Street',
            'payment_method' => 'cod',
            'items' => [
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => 2,
                    'price' => 15000,
                    'total' => 30000,
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.shipping_name', 'John Doe')
            ->assertJsonPath('data.items.0.product_name', 'Luxury Watch');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'shipping_name' => 'John Doe',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }
}
