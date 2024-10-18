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
        // Create 2 orders using the factory
        Order::factory()->count(2)->create();

        // Make a GET request to /api/orders/list
        $response = $this->getJson('/api/orders/list');

        // Verify the response has status 200 and returns 2 orders
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

        // Create a client and two products
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

        // Make a POST request to /api/orders/create with the order data
        $response = $this->postJson('/api/orders/create', $data);

        // Verify the response has status 201 and contains the order data
        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'client_id' => $client->id,
                 ]);

        // Verify the order was saved in the database
        $order = Order::first();
        $this->assertNotNull($order);

        // Verify the products were associated correctly
        $this->assertEquals(2, $order->products()->where('product_id', $product1->id)->first()->pivot->quantity);
        $this->assertEquals(1, $order->products()->where('product_id', $product2->id)->first()->pivot->quantity);

        // Verify the email was sent to the client
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
        // Create an order
        $order = Order::factory()->create();

        // Make a GET request to /api/orders/detail/{id}
        $response = $this->getJson("/api/orders/detail/{$order->id}");

        // Verify the response has status 200 and contains the order data
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
        // Create a client and three products
        $client = Client::factory()->create();
        $product1 = Product::factory()->create(['price' => 5.00]);
        $product2 = Product::factory()->create(['price' => 7.50]);
        $product3 = Product::factory()->create(['price' => 10.00]);

        // Create an order with products
        $order = Order::factory()->create(['client_id' => $client->id]);
        $order->products()->attach([
            $product1->id => ['quantity' => 2],
            $product2->id => ['quantity' => 1],
        ]);

        // Updated data
        $data = [
            'products' => [
                [
                    'id' => $product1->id,
                    'quantity' => 3, // Update quantity
                ],
                [
                    'id' => $product3->id,
                    'quantity' => 1, // Add a new product
                ],
            ],
        ];

        // Make a PUT request to /api/orders/detail/{id} with updated data
        $response = $this->putJson("/api/orders/detail/{$order->id}", $data);

        // Verify the response has status 200
        $response->assertStatus(200);

        // Verify the quantities were updated in the database
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
        // Create an order
        $order = Order::factory()->create();

        // Make a DELETE request to /api/orders/delete/{id}
        $response = $this->deleteJson("/api/orders/delete/{$order->id}");

        // Verify the response has status 200 and contains the success message
        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Order deleted successfully.',
                 ]);

        // Verify the order was soft-deleted in the database
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
        // Create and soft-delete an order
        $order = Order::factory()->create();
        $order->delete();

        // Make a POST request to /api/orders/restore/{id}
        $response = $this->postJson("/api/orders/restore/{$order->id}");

        // Verify the response has status 200 and contains the success message
        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Order restored successfully.',
                 ]);

        // Verify the order was restored in the database
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'deleted_at' => null]);
    }
}
