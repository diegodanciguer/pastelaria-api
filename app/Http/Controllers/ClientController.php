<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Lists all clients.
     */
    public function list()
    {
        $clients = Client::all();
        return response()->json($clients, 200);
    }

    /**
     * Creates a new client.
     */
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'address_line2' => 'nullable|string',
            'neighborhood' => 'required|string',
            'postal_code' => 'required|string|max:10',
        ]);

        $client = Client::create($validatedData);

        return response()->json($client, 201);
    }

    /**
     * Displays details of a specific client.
     */
    public function show($id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json(['message' => 'Client not found.'], 404);
        }

        return response()->json($client, 200);
    }

    /**
     * Updates a specific client.
     */
    public function update(Request $request, $id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json(['message' => 'Client not found.'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:clients,email,' . $id,
            'phone' => 'sometimes|required|string|max:20',
            'date_of_birth' => 'sometimes|required|date',
            'address' => 'sometimes|required|string',
            'address_line2' => 'nullable|string',
            'neighborhood' => 'sometimes|required|string',
            'postal_code' => 'sometimes|required|string|max:10',
        ]);

        $client->update($validatedData);

        return response()->json($client, 200);
    }

    /**
     * Deletes a specific client (Soft Delete).
     */
    public function delete($id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json(['message' => 'Client not found.'], 404);
        }

        $client->delete();

        return response()->json(['message' => 'Client deleted successfully.'], 200);
    }

    /**
     * Restores a deleted client.
     */
    public function restore($id)
    {
        $client = Client::withTrashed()->find($id);

        if (!$client) {
            return response()->json(['message' => 'Client not found.'], 404);
        }

        if (!$client->trashed()) {
            return response()->json(['message' => 'Client is not deleted.'], 400);
        }

        $client->restore();

        return response()->json(['message' => 'Client restored successfully.'], 200);
    }
}
