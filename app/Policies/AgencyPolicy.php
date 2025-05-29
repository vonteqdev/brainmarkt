<?php

namespace App\Policies;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Auth\Access\Response; // Import Response

class AgencyPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Agency $agency): Response
    {
        return $user->agency_id === $agency->id
            ? Response::allow()
            : Response::deny('You do not own this agency.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Agency $agency): Response
    {
        return $user->agency_id === $agency->id && $user->hasRole('agency_admin')
            ? Response::allow()
            : Response::deny('You do not have permission to update this agency.');
    }

    /**
     * Determine whether the user can manage team members for this agency.
     * This is a custom policy method, not a standard CRUD one.
     */
    public function manageTeamMembers(User $user, Agency $agency): Response
    {
        return $user->agency_id === $agency->id && $user->hasRole('agency_admin')
            ? Response::allow()
            : Response::deny('You do not have permission to manage team members for this agency.');
    }

    // Add other policy methods as needed (e.g., viewBilling, manageSettings)
    // public function viewBilling(User $user, Agency $agency): bool { ... }
}
