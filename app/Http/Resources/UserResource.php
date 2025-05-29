<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // Assuming users.id is UUID, otherwise $this->id
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'profile_picture_url' => $this->profile_picture_url,
            'last_login_at' => $this->last_login_at ? $this->last_login_at->toIso8601String() : null,
            'role' => new RoleResource($this->whenLoaded('role')), // Include role if loaded
            // Conditionally load agency only if it's not the current user's agency context
            // 'agency_id' => $this->agency_id, // Or include full agency details if needed
            // 'agency' => new AgencyResource($this->whenLoaded('agency')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
