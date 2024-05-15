<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lastMessage = $this->chat->messages->last();
        $detail = $this->receiver->detailInfo;

        return [
            'room_id' => $this->id,
            'receiver' => [
                'id' => $this->receiver->id,
                'login' => $this->receiver->login,
                'details' => [
                    'name' => $detail->name,
                    'avatar' => $detail->avatar,
                ]
            ],
            'chat' => [
                'id' => $this->chat->id,
                'last_message' => [
                    'body' => $lastMessage->body,
                    'user_id' => $lastMessage->user_id,
                ],
            ]
        ];
    }
}
