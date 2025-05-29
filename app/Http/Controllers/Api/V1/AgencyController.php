<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AgencyResource; // Create this resource: php artisan make:resource AgencyResource
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AgencyController extends Controller
{
    public function __construct()
    {
        // Apply authorization. Assumes AgencyPolicy exists.
        // $this->authorizeResource(Agency::class, 'agency'); // Not a standard resource controller
    }

    public function show(Request $request): AgencyResource
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $agency = $user->agency()->firstOrFail(); // Ensure agency exists for the user

        $this->authorize('view', $agency); // Uses AgencyPolicy@view

        return new AgencyResource($agency);
    }

    public function update(Request $request): AgencyResource
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $agency = $user->agency()->firstOrFail();

        $this->authorize('update', $agency); // Uses AgencyPolicy@update

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('agencies')->ignore($agency->id),
            ],
            'phone_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'logo_url' => 'nullable|url|max:512',
            'timezone' => 'sometimes|required|string|max:100',
            'currency_preference' => 'sometimes|required|string|max:10',
        ]);

        $agency->update($validatedData);

        return new AgencyResource($agency->fresh());
    }
}
