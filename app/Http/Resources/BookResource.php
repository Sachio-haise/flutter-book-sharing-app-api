<?php

namespace App\Http\Resources;

use App\Models\Cart;
use App\Models\MediaStorage;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'description' => $this->description,
            'review' => $this->review,
            'status' => $this->status,
            'photo' => new MediaResource(MediaStorage::findOrFail($this->photo_id)),
            'book' => new MediaResource(MediaStorage::findOrFail($this->book_id)),
            'user' => new UserResource(User::where('id', $this->user_id)->first()),
            'reactions' => Reaction::where('book_id', $this->id)->count(),
            'carts' => Cart::where('book_id', $this->id)->pluck('user_id'),
            'react_user' => Reaction::where('book_id', $this->id)->pluck('user_id'),
        ];
    }
}
