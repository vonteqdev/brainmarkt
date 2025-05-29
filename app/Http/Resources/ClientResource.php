<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'agency_id' => $this->agency_id, // Useful for context
            'name' => $this->name,
            'website' => $this->website,
            'industry' => $this->industry,
            'contact_person' => $this->contact_person,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            // Example of including related data summary
            // 'feed_source_count' => $this->whenLoaded('feedSources', fn() => $this->feedSources->count()),
        ];
    }
}
