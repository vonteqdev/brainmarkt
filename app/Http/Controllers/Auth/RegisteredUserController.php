<?php

namespace App\Http\Controllers\Auth; // Or App\Http\Controllers\Api\V1\Auth if you moved it & updated routes

use App\Http\Controllers\Controller;
use App\Models\Agency; // Import Agency model
use App\Models\User;
use App\Models\Role;   // Import Role model
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
// Use JsonResponse for API consistency
use Illuminate\Http\JsonResponse;
// It's good practice to alias Auth facade if your controller has a conflicting name, though not strictly necessary here
// use Illuminate\Support\Facades\Auth as AuthFacade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB; // For database transactions
use App\Http\Resources\UserResource; // To format the user response

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse // Changed to JsonResponse
    {
        $request->validate([
            'agency_name' => ['required', 'string', 'max:255', 'unique:agencies,name'], // Validate agency_name
            'name' => ['required', 'string', 'max:255'],
            // For SaaS, user email should probably be globally unique to avoid confusion,
            // or unique per agency if users can belong to multiple agencies with same email (more complex)
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'timezone' => ['required', 'string', 'max:100'], // Validate timezone
        ]);

        // Use a database transaction to ensure both agency and user are created, or neither.
        DB::beginTransaction();

        try {
            // 1. Create the Agency
            $agency = Agency::create([
                'name' => $request->agency_name,
                'contact_email' => $request->email, // Use user's email as agency contact initially
                'contact_person_name' => $request->name, // Use user's name as agency contact initially
                'timezone' => $request->timezone,
                // Add other default agency fields if necessary from your 'agencies' migration
                // e.g., 'currency_preference' => 'USD',
            ]);

            // 2. Find the default role for the agency creator (e.g., 'agency_admin')
            // Make sure 'agency_admin' role is created by your RoleSeeder
            $agencyAdminRole = Role::where('name', 'agency_admin')->first();

            if (!$agencyAdminRole) {
                // This is a critical setup error. The role should exist.
                DB::rollBack();
                // Log this error for admins
                \Log::error('Critical setup error: agency_admin role not found during registration.');
                return response()->json(['message' => 'Server configuration error. Please contact support.'], 500);
            }

            // 3. Create the User and associate with the Agency and Role
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'agency_id' => $agency->id, // Assign the new agency's ID
                'role_id' => $agencyAdminRole->id, // Assign the 'agency_admin' role ID
                'is_active' => true, // Or false then send verification email
                // 'timezone' => $request->timezone, // If you have a timezone column on users table
            ]);

            event(new Registered($user)); // Fires event for email verification, etc.

            // For an API, you typically don't log the user in immediately with Auth::login($user);
            // The frontend will make a separate login request after registration if needed,
            // or if email verification is required, user logs in after verification.
            // If you want to return an API token for immediate use (Sanctum token-based, not SPA cookie):
            // $token = $user->createToken('brainmarkt-registration-token')->plainTextToken;

            DB::commit();

            // Return a success response with the created user (transformed by UserResource)
            return response()->json([
                'message' => 'Agency and user registered successfully.',
                'user' => new UserResource($user->load('agency', 'role'))
            ], 201); // HTTP 201 Created

        } catch (\Illuminate\Validation\ValidationException $e) {
            // This catch block might be redundant if Laravel's default exception handler
            // already converts ValidationException to a JSON response.
            // However, explicit handling can be useful.
            DB::rollBack();
            return response()->json(['message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the detailed exception for debugging
            \Log::error('Registration Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Registration failed due to a server error. Please try again later.'], 500);
        }
    }
}
