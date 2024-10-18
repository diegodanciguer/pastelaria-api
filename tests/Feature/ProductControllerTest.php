<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tests listing all products.
     */
    public function testListProducts()
    {
        Product::factory()->count(5)->create();

        $response = $this->getJson('/api/products/list');

        $response->assertStatus(200)
                 ->assertJsonCount(5);
    }

    /**
     * Tests creating a new product with image.
     */
    public function testCreateProduct()
    {
        Storage::fake('public');

        $data = [
            'name' => 'Beef Pastel',
            'price' => 5.50,
            'image' => UploadedFile::fake()->image('beef_pastel.jpg'),
        ];

        $response = $this->postJson('/api/products/create', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'name' => 'Beef Pastel',
                     'price' => 5.50,
                 ]);

        $product = Product::first();
        $this->assertNotNull($product);

        Storage::disk('public')->assertExists($product->image);
    }

    /**
     * Tests creating a new product without image.
     */
    public function testCreateProductWithoutImage()
    {
        $data = [
            'name' => 'Cheese Pastel',
            'price' => 4.00,
            // 'image' is not provided
        ];

        $response = $this->postJson('/api/products/create', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['image']);
    }

    /**
     * Tests showing details of a specific product.
     */
    public function testShowProduct()
    {
        $product = Product::factory()->create([
            'name' => 'Sample Product',
            'price' => 3.81,
        ]);

        $response = $this->getJson("/api/products/detail/{$product->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'name' => $product->name,
                     'price' => 3.81,
                 ]);
    }

    /**
     * Tests updating a specific product's name and price.
     */
    public function testUpdateProduct()
    {
        Storage::fake('public');

        $product = Product::factory()->create([
            'name' => 'Chicken Pastel',
            'price' => 6.00,
            'image' => 'images/chicken_pastel.jpg',
        ]);

        Storage::disk('public')->put('images/chicken_pastel.jpg', 'dummy content');

        $newImage = UploadedFile::fake()->image('chicken_pastel_catupiry.jpg');

        $response = $this->putJson("/api/products/detail/{$product->id}", [
            'name' => 'Chicken Pastel with Catupiry',
            'price' => 6.50,
            'image' => $newImage,
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'name' => 'Chicken Pastel with Catupiry',
                     'price' => 6.50, // Expects a number
                 ]);

        $product->refresh();

        $this->assertEquals('Chicken Pastel with Catupiry', $product->name);
        $this->assertEquals(6.50, $product->price);

        Storage::disk('public')->assertExists($product->image);

        Storage::disk('public')->assertMissing('images/chicken_pastel.jpg');
    }

    /**
     * Tests deleting a specific product (Soft Delete).
     */
    public function testDeleteProduct()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/delete/{$product->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Product deleted successfully.',
                 ]);

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    /**
     * Tests restoring a deleted product.
     */
    public function testRestoreProduct()
    {
        $product = Product::factory()->create();
        $product->delete();

        $response = $this->postJson("/api/products/restore/{$product->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Product restored successfully.',
                 ]);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'deleted_at' => null]);
    }

    /**
     * Tests attempting to restore a non-deleted product.
     */
    public function testRestoreNonDeletedProduct()
    {
        $product = Product::factory()->create();

        $response = $this->postJson("/api/products/restore/{$product->id}");

        $response->assertStatus(400)
                 ->assertJsonFragment([
                     'message' => 'Product is not deleted.',
                 ]);
    }

    /**
     * Tests showing a non-existent product.
     */
    public function testShowNonExistentProduct()
    {
        $response = $this->getJson('/api/products/detail/999');

        $response->assertStatus(404)
                 ->assertJsonFragment([
                     'message' => 'Product not found.',
                 ]);
    }
}
