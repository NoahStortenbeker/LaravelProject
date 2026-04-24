<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmed;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_checkout(): void
    {
        $response = $this->get('/checkout');

        $response->assertRedirect('/login');
    }

    public function test_user_can_access_checkout(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/checkout');

        $response->assertStatus(200);
    }

    public function test_user_can_checkout_and_pay(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $p1 = Product::create([
            'category' => 'Sneakers',
            'name' => 'Test Shoe',
            'price' => 100,
            'description' => null,
            'images' => [],
            'is_online' => true,
            'discount_percent' => 10,
            'created_by' => $user->id,
        ]);
        $p2 = Product::create([
            'category' => 'Accessories',
            'name' => 'Test Cap',
            'price' => 50,
            'description' => null,
            'images' => [],
            'is_online' => true,
            'discount_percent' => 0,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->post('/checkout', [
            'shipping_name' => 'Noah',
            'shipping_email' => 'noah@example.com',
            'shipping_phone' => '0612345678',
            'shipping_address_line' => 'De Raatdweg 11',
            'shipping_zip_code' => '3341 SB',
            'shipping_city' => 'Hendrik-Ido-Ambacht',
            'shipping_country' => 'Netherlands',
            'notes' => 'Leave at door',
            'cart_json' => json_encode([
                ['id' => $p1->id, 'qty' => 2],
                ['id' => $p2->id, 'qty' => 1],
            ]),
        ]);

        $order = Order::query()->firstOrFail();

        $response->assertRedirect(route('checkout.payment', ['order' => $order->id], absolute: false));
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'product_id' => $p1->id, 'qty' => 2]);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'product_id' => $p2->id, 'qty' => 1]);

        $pay = $this->actingAs($user)->post(route('checkout.pay', ['order' => $order->id], absolute: false));
        $pay->assertRedirect(route('checkout.confirmation', ['order' => $order->id], absolute: false));

        $order->refresh();
        $this->assertSame('processing', $order->status);
        $this->assertNotNull($order->paid_at);

        Mail::assertSent(OrderConfirmed::class);
    }

    public function test_user_can_cancel_pending_order(): void
    {
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending_payment',
            'currency' => 'EUR',
            'subtotal' => 10,
            'total' => 10,
            'shipping_name' => 'Noah',
            'shipping_email' => 'noah@example.com',
            'shipping_address_line' => 'Street 1',
            'shipping_zip_code' => '0000AA',
            'shipping_city' => 'City',
            'shipping_country' => 'Netherlands',
        ]);

        $response = $this->actingAs($user)->post(route('users.orders.cancel', ['order' => $order->id], absolute: false));
        $response->assertRedirect(route('users.dashboard', ['panel' => 'orders'], absolute: false));

        $order->refresh();
        $this->assertSame('canceling', $order->status);
    }
}
