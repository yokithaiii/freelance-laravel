<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatOfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $job = $this->job;

        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'executor_id' => $this->executor_id,
            'offer_date_deadline_days' => $this->offer_date_deadline_days,
            'offer_description' => $this->offer_description,
            'offer_price' => $this->offer_price,
            'offer_status' => $this->offer_status,
            'offer_created' => $this->created_at->format('d.m.Y'),
            'offer_job' => [
                'id' => $job->id,
                'title' => $job->title,
                'description' => $job->description,
                'date_deadline' => $job->date_deadline,
                'price' => $job->price,
                'price_in_hour_flag' => $job->price_in_hour_flag,
            ]
        ];
    }
}
