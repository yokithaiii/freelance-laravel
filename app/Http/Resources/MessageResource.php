<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'body' => $this->body,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->detailInfo->name,
                'avatar' => $this->user->detailInfo->avatar,
            ],
            'created_at' => $this->created_at->diffForHumans(),
        ];

    }
}
