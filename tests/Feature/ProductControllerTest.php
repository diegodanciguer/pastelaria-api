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
        // Creates 5 products using the factory
        Product::factory()->count(5)->create();

        // Makes a GET request to /api/products/list
        $response = $this->getJson('/api/products/list');

        // Checks if the response status is 200 and returns 5 products
        $response->assertStatus(200)
                 ->assertJsonCount(5);
    }

    /**
     * Tests creating a new product with image.
     */
    public function testCreateProduct()
    {
        // Simulates the 'public' disk
        Storage::fake('public');

        $data = [
            'name' => 'Beef Pastel',
            'price' => 5.50,
            'image' => UploadedFile::fake()->image('beef_pastel.jpg'),
        ];

        // Makes a POST request to /api/products/create with product data
        $response = $this->postJson('/api/products/create', $data);

        // Checks if the response has status 201 and contains the product data
        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'name' => 'Beef Pastel',
                     'price' => 5.50, // Expects a number
                 ]);

        // Checks if the product was saved in the database
        $product = Product::first();
        $this->assertNotNull($product);

        // Checks if the image was stored correctly using the static method
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

        // Makes a POST request to /api/products/create without image
        $response = $this->postJson('/api/products/create', $data);

        // Expects a validation error for the 'image' field
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['image']);
    }

    /**
     * Tests showing details of a specific product.
     */
    public function testShowProduct()
    {
        // Creates a product with specific price
        $product = Product::factory()->create([
            'name' => 'Sample Product',
            'price' => 3.81,
        ]);

        // Makes a GET request to /api/products/detail/{id}
        $response = $this->getJson("/api/products/detail/{$product->id}");

        // Checks if the response has status 200 and contains the product data
        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'name' => $product->name,
                     'price' => 3.81, // Expects a number
                 ]);
    }

    /**
     * Tests updating a specific product's name and price.
     */
    public function testUpdateProduct()
    {
        // Simulates the 'public' disk
        Storage::fake('public');

        // Creates an existing product with an initial image
        $product = Product::factory()->create([
            'name' => 'Chicken Pastel',
            'price' => 6.00,
            'image' => 'images/chicken_pastel.jpg',
        ]);

        // Simulates the existence of the old image
        Storage::disk('public')->put('images/chicken_pastel.jpg', 'dummy content');

        // Simulates uploading a new image
        $newImage = UploadedFile::fake()->image('chicken_pastel_catupiry.jpg');

        // Makes a PUT request to /api/products/detail/{id} with updated data
        $response = $this->putJson("/api/products/detail/{$product->id}", [
            'name' => 'Chicken Pastel with Catupiry',
            'price' => 6.50,
            'image' => $newImage,
        ]);

        // Checks if the response has status 200 and contains the updated data
        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'name' => 'Chicken Pastel with Catupiry',
                     'price' => 6.50, // Expects a number
                 ]);

        // Refreshes the model to reflect changes in the database
        $product->refresh();

        // Checks if the product was updated in the database
        $this->assertEquals('Chicken Pastel with Catupiry', $product->name);
        $this->assertEquals(6.50, $product->price);

        // Checks if the new image was stored correctly
        Storage::disk('public')->assertExists($product->image);

        // Checks if the old image was removed
        Storage::disk('public')->assertMissing('images/chicken_pastel.jpg');
    }

    /**
     * Tests deleting a specific product (Soft Delete).
     */
    public function testDeleteProduct()
    {
        // Creates a product
        $product = Product::factory()->create();

        // Makes a DELETE request to /api/products/delete/{id}
        $response = $this->deleteJson("/api/products/delete/{$product->id}");

        // Checks if the response has status 200 and contains the success message
        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Product deleted successfully.',
                 ]);

        // Checks if the product was soft-deleted in the database
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    /**
     * Tests restoring a deleted product.
     */
    public function testRestoreProduct()
    {
        // Creates and soft-deletes a product
        $product = Product::factory()->create();
        $product->delete();

        // Makes a POST request to /api/products/restore/{id}
        $response = $this->postJson("/api/products/restore/{$product->id}");

        // Checks if the response has status 200 and contains the success message
        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Product restored successfully.',
                 ]);

        // Checks if the product was restored in the database
        $this->assertDatabaseHas('products', ['id' => $product->id, 'deleted_at' => null]);
    }

    /**
     * Tests attempting to restore a non-deleted product.
     */
    public function testRestoreNonDeletedProduct()
    {
        // Creates a product
        $product = Product::factory()->create();

        // Makes a POST request to /api/products/restore/{id} without deleting first
        $response = $this->postJson("/api/products/restore/{$product->id}");

        // Checks if the response has status 400 and contains the appropriate message
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
        // Makes a GET request to /api/products/detail/999 (assuming 999 does not exist)
        $response = $this->getJson('/api/products/detail/999');

        // Checks if the response has status 404 and contains the appropriate message
        $response->assertStatus(404)
                 ->assertJsonFragment([
                     'message' => 'Product not found.',
                 ]);
    }
}
