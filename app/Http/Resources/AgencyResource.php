<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'contact_person_name' => $this->contact_person_name,
            'contact_email' => $this->contact_email,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'website' => $this->website,
            'logo_url' => $this->logo_url,
            'timezone' => $this->timezone,
            'currency_preference' => $this->currency_preference,
            // 'subscription_status' => $this->subscription_status, // If implemented
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            // Example of including counts conditionally (load these relationships in controller if needed)
            'client_count' => $this->whenLoaded('clients', fn() => $this->clients->count()),
            'team_member_count' => $this->whenLoaded('users', fn() => $this->users->count()),
        ];
    }
}
