<?php

namespace App\Http\Resources;

use App\Models\MediaStorage;
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
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'profile' => $this->profile_id ? new MediaResource(MediaStorage::findOrFail($this->profile_id)) : null,
            'description' => $this->description,
            'created_at' => $this->created_at
        ];
    }
}
