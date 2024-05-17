<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->user;
        return [
            'id' => $this->id,
            'service_title' => $this->service_title,
            'service_description' => $this->service_description,
            'service_needs' => $this->service_needs,
            'service_price' => $this->service_price,
            'service_term_days' => $this->service_term_days,
            'cover' => [
                'file_name' => $this->cover->file_name,
                'file_path' => $this->cover->file_path,
            ],
            'images' => $this->images,
            'user' => [
                'id' => $user->id,
                'login' => $user->login,
                'email' => $user->email,
                'details' => [
                    'name' => $user->detailInfo->name,
                    'profession' => $user->detailInfo->profession,
                    'avatar' => $user->detailInfo->avatar,
                ]
            ]
        ];
    }
}
