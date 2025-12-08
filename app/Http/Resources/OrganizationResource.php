<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class OrganizationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'phones' => PhoneResource::collection($this->whenLoaded('phones')),
            'building' => new BuildingResource($this->whenLoaded('building')),
            'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
