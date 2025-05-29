<?php

namespace App\Policies;

use App\Models\User; // Target User model
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the current user (actingUser) can view any team members within their agency.
     * This policy method is for the User model itself, when $user is the one performing action
     * and $model is the target user.
     */
    public function viewAny(User $actingUser): Response
    {
        // An agency admin or someone with 'manage_team_members' can list users of their agency
        return ($actingUser->hasRole('agency_admin') || $actingUser->hasPermissionTo('manage_team_members'))
            ? Response::allow()
            : Response::deny('You do not have permission to view team members.');
    }

    /**
     * Determine whether the current user (actingUser) can view a specific team member's profile (targetUser).
     */
    public function view(User $actingUser, User $targetUser): Response
    {
        // Can view if they are in the same agency AND actingUser has permission
        return $actingUser->agency_id === $targetUser->agency_id &&
               ($actingUser->hasRole('agency_admin') || $actingUser->hasPermissionTo('manage_team_members'))
            ? Response::allow()
            : Response::deny('You do not have permission to view this team member.');
    }

    /**
     * Determine whether the current user (actingUser) can invite new team members to their agency.
     * This isn't tied to a specific targetUser model instance, so often handled by a Gate or a general permission check.
     * However, if we want to use UserPolicy for this context:
     */
    // public function create(User $actingUser): Response // 'create' usually refers to creating the model instance
    // {
    //     return ($actingUser->hasRole('agency_admin') || $actingUser->hasPermissionTo('manage_team_members'))
    //         ? Response::allow()
    //         : Response::deny('You do not have permission to invite team members.');
    // }
    // A custom method might be better or a Gate for 'invite'

    /**
     * Determine whether the current user (actingUser) can update a team member's details (targetUser).
     */
    public function update(User $actingUser, User $targetUser): Response
    {
        // Cannot edit self through this policy method (usually handled in controller or separate 'updateProfile' policy method)
        // Must be in same agency AND actingUser has permission
        return $actingUser->id !== $targetUser->id &&
               $actingUser->agency_id === $targetUser->agency_id &&
               ($actingUser->hasRole('agency_admin') || $actingUser->hasPermissionTo('manage_team_members'))
            ? Response::allow()
            : Response::deny('You do not have permission to update this team member.');
    }

    /**
     * Determine whether the current user (actingUser) can deactivate/delete a team member (targetUser).
     */
    public function delete(User $actingUser, User $targetUser): Response
    {
        // Cannot delete self, must be in same agency AND actingUser has permission
        return $actingUser->id !== $targetUser->id &&
               $actingUser->agency_id === $targetUser->agency_id &&
               ($actingUser->hasRole('agency_admin') || $actingUser->hasPermissionTo('manage_team_members'))
            ? Response::allow()
            : Response::deny('You do not have permission to delete this team member.');
    }
}
