<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test listing all clients.
     *
     * @return void
     */
    public function testListClients()
    {
        Client::factory()->count(3)->create();

        $response = $this->getJson('/api/clients/list');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /**
     * Test creating a new client.
     *
     * @return void
     */
    public function testCreateClient()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
            'date_of_birth' => '1990-01-01',
            'address' => '123 Main St',
            'address_line2' => 'Apt 4B',
            'neighborhood' => 'Downtown',
            'postal_code' => '12345-678',
        ];

        $response = $this->postJson('/api/clients/create', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'name' => 'John Doe',
                     'email' => 'john.doe@example.com',
                 ]);

        $this->assertDatabaseHas('clients', [
            'email' => 'john.doe@example.com',
        ]);
    }

    /**
     * Test showing details of a specific client.
     *
     * @return void
     */
    public function testShowClient()
    {
        $client = Client::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane.smith@example.com',
        ]);

        $response = $this->getJson("/api/clients/detail/{$client->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'name' => 'Jane Smith',
                     'email' => 'jane.smith@example.com',
                 ]);
    }

    /**
     * Test updating a specific client.
     *
     * @return void
     */
    public function testUpdateClient()
    {
        $client = Client::factory()->create([
            'name' => 'Alice Johnson',
            'email' => 'alice.johnson@example.com',
        ]);

        $data = [
            'name' => 'Alice Brown',
            'email' => 'alice.brown@example.com',
        ];

        $response = $this->putJson("/api/clients/detail/{$client->id}", $data);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'name' => 'Alice Brown',
                     'email' => 'alice.brown@example.com',
                 ]);

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Alice Brown',
            'email' => 'alice.brown@example.com',
        ]);
    }

    /**
     * Test deleting a specific client (Soft Delete).
     *
     * @return void
     */
    public function testDeleteClient()
    {
        $client = Client::factory()->create();

        $response = $this->deleteJson("/api/clients/delete/{$client->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Client deleted successfully.',
                 ]);

        $this->assertSoftDeleted('clients', [
            'id' => $client->id,
        ]);
    }

    /**
     * Test restoring a deleted client.
     *
     * @return void
     */
    public function testRestoreClient()
    {
        $client = Client::factory()->create();
        $client->delete();

        $response = $this->postJson("/api/clients/restore/{$client->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Client restored successfully.',
                 ]);

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test attempting to restore a non-deleted client.
     *
     * @return void
     */
    public function testRestoreNonDeletedClient()
    {
        $client = Client::factory()->create();

        $response = $this->postJson("/api/clients/restore/{$client->id}");

        $response->assertStatus(400)
                 ->assertJsonFragment([
                     'message' => 'Client is not deleted.',
                 ]);
    }

    /**
     * Test showing a non-existent client.
     *
     * @return void
     */
    public function testShowNonExistentClient()
    {
        $response = $this->getJson('/api/clients/detail/999');

        $response->assertStatus(404)
                 ->assertJsonFragment([
                     'message' => 'Client not found.',
                 ]);
    }
}
