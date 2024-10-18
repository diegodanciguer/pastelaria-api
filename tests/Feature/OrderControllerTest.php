<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Client;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderCreatedMail;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tests listing all orders.
     *
     * @return void
     */
    public function testListOrders()
    {
        Order::factory()->count(2)->create();

        $response = $this->getJson('/api/orders/list');

        $response->assertStatus(200)
                 ->assertJsonCount(2);
    }

    /**
     * Tests creating a new order and sending the email.
     *
     * @return void
     */
    public function testCreateOrder()
    {
        Mail::fake();

        $client = Client::factory()->create();
        $product1 = Product::factory()->create(['price' => 5.00]);
        $product2 = Product::factory()->create(['price' => 7.50]);

        $data = [
            'client_id' => $client->id,
            'products' => [
                [
                    'id' => $product1->id,
                    'quantity' => 2,
                ],
                [
                    'id' => $product2->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders/create', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'client_id' => $client->id,
                 ]);

        $order = Order::first();
        $this->assertNotNull($order);

        $this->assertEquals(2, $order->products()->where('product_id', $product1->id)->first()->pivot->quantity);
        $this->assertEquals(1, $order->products()->where('product_id', $product2->id)->first()->pivot->quantity);

        Mail::assertSent(OrderCreatedMail::class, function ($mail) use ($order) {
            return $mail->order->id === $order->id && $mail->hasTo($order->client->email);
        });
    }

    /**
     * Tests displaying details of a specific order.
     *
     * @return void
     */
    public function testShowOrder()
    {
        $order = Order::factory()->create();

        $response = $this->getJson("/api/orders/detail/{$order->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $order->id,
                     'client_id' => $order->client_id,
                 ]);
    }

    /**
     * Tests updating a specific order.
     *
     * @return void
     */
    public function testUpdateOrder()
    {
        $client = Client::factory()->create();
        $product1 = Product::factory()->create(['price' => 5.00]);
        $product2 = Product::factory()->create(['price' => 7.50]);
        $product3 = Product::factory()->create(['price' => 10.00]);

        $order = Order::factory()->create(['client_id' => $client->id]);
        $order->products()->attach([
            $product1->id => ['quantity' => 2],
            $product2->id => ['quantity' => 1],
        ]);

        $data = [
            'products' => [
                [
                    'id' => $product1->id,
                    'quantity' => 3,
                ],
                [
                    'id' => $product3->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $response = $this->putJson("/api/orders/detail/{$order->id}", $data);

        $response->assertStatus(200);

        $order->refresh();
        $this->assertEquals(3, $order->products()->where('product_id', $product1->id)->first()->pivot->quantity);
        $this->assertEquals(1, $order->products()->where('product_id', $product3->id)->first()->pivot->quantity);
    }

    /**
     * Tests deleting a specific order (Soft Delete).
     *
     * @return void
     */
    public function testDeleteOrder()
    {
        $order = Order::factory()->create();

        $response = $this->deleteJson("/api/orders/delete/{$order->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Order deleted successfully.',
                 ]);

        $this->assertSoftDeleted('orders', [
            'id' => $order->id,
        ]);
    }

    /**
     * Tests restoring a deleted order.
     *
     * @return void
     */
    public function testRestoreOrder()
    {
        $order = Order::factory()->create();
        $order->delete();

        $response = $this->postJson("/api/orders/restore/{$order->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Order restored successfully.',
                 ]);

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'deleted_at' => null]);
    }
}
