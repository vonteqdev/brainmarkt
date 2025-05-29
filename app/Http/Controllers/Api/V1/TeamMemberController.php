<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource; // Create: php artisan make:resource UserResource
use App\Http\Resources\UserCollection; // Create: php artisan make:resource UserCollection
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Auth\Events\Registered; // For sending verification email

class TeamMemberController extends Controller
{
    public function index(Request $request): UserCollection
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        // Use AgencyPolicy's manageTeamMembers method for authorization
        $this->authorize('manageTeamMembers', $currentUser->agency);

        $teamMembers = User::where('agency_id', $currentUser->agency_id)
            ->with('role') // Eager load role
            ->paginate($request->input('per_page', 15));

        return new UserCollection($teamMembers);
    }

    public function invite(Request $request): \Illuminate\Http\JsonResponse
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        $this->authorize('manageTeamMembers', $currentUser->agency); // Or a specific 'inviteTeamMembers' permission

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                // Ensure email is unique within this agency for new users
                Rule::unique('users')->where(function ($query) use ($currentUser) {
                    return $query->where('agency_id', $currentUser->agency_id);
                }),
            ],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')],
        ]);

        $temporaryPassword = Str::random(12);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($temporaryPassword),
            'agency_id' => $currentUser->agency_id,
            'role_id' => $validatedData['role_id'],
            'email_verified_at' => null,
            'is_active' => true, // Or false, requiring admin activation
        ]);

        // event(new Registered($user)); // To send Laravel's default verification email
        // Or, send a custom invitation email with login details/reset link.

        return response()->json([
            'message' => 'Team member invited successfully. They should receive an email to set their password or verify.',
            'user' => new UserResource($user)
        ], 201);
    }

    public function update(Request $request, User $user): UserResource // Route model binding for the team member
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        // Use UserPolicy's update method for authorization
        $this->authorize('update', $user);

        // Redundant check if policy is correctly implemented, but good for clarity
        if ($user->agency_id !== $currentUser->agency_id) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'role_id' => ['sometimes', 'required', 'integer', Rule::exists('roles', 'id')],
            'is_active' => ['sometimes', 'required', 'boolean'],
            // Add other updatable fields like 'name' if needed
            'name' => ['sometimes', 'required', 'string', 'max:255'],
        ]);

        $user->update($validatedData);

        return new UserResource($user->load('role'));
    }

    public function destroy(User $user): \Illuminate\Http\JsonResponse // Route model binding
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        $this->authorize('delete', $user); // Use UserPolicy's delete method

        if ($user->agency_id !== $currentUser->agency_id) {
            abort(403, 'Unauthorized action.');
        }
        if ($user->id === $currentUser->id) {
            abort(403, 'You cannot deactivate or delete yourself.');
        }

        // Option 1: Deactivate
        $user->update(['is_active' => false]);
        $message = 'Team member deactivated successfully.';

        // Option 2: Soft Delete (if User model uses SoftDeletes trait)
        // $user->delete();
        // $message = 'Team member archived successfully.';

        // Option 3: Hard Delete (use with caution)
        // $user->forceDelete();
        // $message = 'Team member permanently deleted.';

        return response()->json(['message' => $message], 200);
    }
}
