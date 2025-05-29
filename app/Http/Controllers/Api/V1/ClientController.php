<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientCollection; // Create: php artisan make:resource ClientCollection
use App\Http\Resources\ClientResource;   // Create: php artisan make:resource ClientResource
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Automatically authorize actions using the ClientPolicy.
     */
    public function __construct()
    {
        $this->authorizeResource(Client::class, 'client');
    }

    public function index(Request $request): ClientCollection
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // Authorization handled by authorizeResource (ClientPolicy@viewAny)

        $clients = $user->agency->clients() // Assuming User model has agency->clients() or agency directly
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('website', 'like', "%{$search}%");
            })
            ->orderBy($request->input('sort_by', 'name'), $request->input('sort_direction', 'asc'))
            ->paginate($request->input('per_page', 15));

        return new ClientCollection($clients);
    }

    public function store(Request $request): ClientResource
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // Authorization handled by authorizeResource (ClientPolicy@create)

        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('clients')->where(function ($query) use ($user) {
                    return $query->where('agency_id', $user->agency_id);
                }),
            ],
            'website' => 'nullable|url|max:255',
            'industry' => 'nullable|string|max:100',
            'contact_person' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        // Ensure agency_id is set correctly from the authenticated user's agency
        $client = $user->agency->clients()->create(array_merge($validatedData, ['agency_id' => $user->agency_id]));

        return new ClientResource($client);
    }

    public function show(Client $client): ClientResource // Route model binding
    {
        // Authorization handled by authorizeResource (ClientPolicy@view)
        // Eager load relationships you want to include in the ClientResource if not always loaded
        return new ClientResource($client->loadMissing([]));
    }

    public function update(Request $request, Client $client): ClientResource
    {
        // Authorization handled by authorizeResource (ClientPolicy@update)
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('clients')->where(function ($query) use ($user) {
                    return $query->where('agency_id', $user->agency_id);
                })->ignore($client->id),
            ],
            'website' => 'nullable|url|max:255',
            'industry' => 'nullable|string|max:100',
            'contact_person' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $client->update($validatedData);

        return new ClientResource($client);
    }

    public function destroy(Client $client): \Illuminate\Http\JsonResponse
    {
        // Authorization handled by authorizeResource (ClientPolicy@delete)
        $client->delete(); // Soft delete if using SoftDeletes trait in Client model
        return response()->json(['message' => 'Client archived successfully.'], 200); // Or 204 No Content
    }
}
