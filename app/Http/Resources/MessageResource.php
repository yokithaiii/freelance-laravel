<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
        Carbon::setLocale('ru');

        return [
            'id' => $this->id,
            'body' => $this->body,
            'created_date' => $this->created_at->format('d.m.Y'),
            'created_time' => $this->created_at->format('H:i'),
            'is_image' => $this->image ? true : false,
            'image' => [
                'file_name' => $this->image ? $this->image->file_name : null,
                'file_path' => $this->image ? $this->image->file_path : null,
                'file_caption' => $this->image ? $this->image->file_caption : null,
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->detailInfo->name,
                'avatar' => $this->user->detailInfo->avatar,
            ],
        ];

    }
}
