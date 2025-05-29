<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClientPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        // User must belong to an agency and have 'view_clients' permission or be an admin/manager
        return $user->agency_id !== null &&
               ($user->hasRole('agency_admin') || $user->hasRole('agency_manager') || $user->hasPermissionTo('view_clients'))
            ? Response::allow()
            : Response::deny('You do not have permission to view clients.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Client $client): Response
    {
        return $user->agency_id === $client->agency_id &&
               ($user->hasRole('agency_admin') || $user->hasRole('agency_manager') || $user->hasPermissionTo('view_clients'))
            ? Response::allow()
            : Response::deny('You do not have permission to view this client.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->agency_id !== null &&
               ($user->hasRole('agency_admin') || $user->hasRole('agency_manager') || $user->hasPermissionTo('create_clients'))
            ? Response::allow()
            : Response::deny('You do not have permission to create clients.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Client $client): Response
    {
        return $user->agency_id === $client->agency_id &&
               ($user->hasRole('agency_admin') || $user->hasRole('agency_manager') || $user->hasPermissionTo('edit_clients'))
            ? Response::allow()
            : Response::deny('You do not have permission to update this client.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Client $client): Response
    {
        return $user->agency_id === $client->agency_id &&
               ($user->hasRole('agency_admin') || $user->hasPermissionTo('delete_clients'))
            ? Response::allow()
            : Response::deny('You do not have permission to delete this client.');
    }

    /**
     * Determine whether the user can restore the model. (If using SoftDeletes)
     */
    public function restore(User $user, Client $client): Response
    {
        return $user->agency_id === $client->agency_id && $user->hasRole('agency_admin') // Typically admin action
            ? Response::allow()
            : Response::deny('You do not have permission to restore this client.');
    }

    /**
     * Determine whether the user can permanently delete the model. (If using SoftDeletes)
     */
    public function forceDelete(User $user, Client $client): Response
    {
        return $user->agency_id === $client->agency_id && $user->hasRole('agency_admin') // Typically admin action
            ? Response::allow()
            : Response::deny('You do not have permission to permanently delete this client.');
    }
}
