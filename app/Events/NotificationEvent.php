<?php

namespace App\Events;

use App\Models\Job;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $offer;
    public $job;
    public $user;
    public $userDetailInfo;

    /**
     * Create a new event instance.
     */
    public function __construct($offer)
    {
        $this->offer = $offer;
        $this->job = Job::find($offer->job_id);
        $this->user = User::find($offer->executor_id);
        $this->userDetailInfo = $this->user->detailInfo;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('notifications')
        ];
    }

    public function broadcastAs()
    {
        return 'notification';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->offer->id,
            'offer_description' => $this->offer->offer_description,
            'offer_price' => $this->offer->offer_price,
            'offer_date_deadline_days' => $this->offer->offer_date_deadline_days,
            'offer_status' => $this->offer->offer_status,
            'job_id' => $this->offer->job_id,
            'customer_id' => $this->offer->customer_id,
            'executor_id' => $this->offer->executor_id,
            'created_at' => $this->offer->created_at,
            'updated_at' => $this->offer->updated_at,
            'user' => [
                'id' => $this->user->id,
                'login' => $this->user->login,
                'email' => $this->user->email,
                'email_verified_at' => $this->user->email_verified_at,
                'created_at' => $this->user->created_at,
                'updated_at' => $this->user->updated_at,
                'detail_info' => $this->user->detailInfo,
            ],
            'job' => [
                'id' => $this->job->id,
                'title' => $this->job->title,
                'description' => $this->job->description,
                'price' => $this->job->price,
                'price_in_hour_flag' => $this->job->price_in_hour_flag,
                'date_deadline' => $this->job->date_deadline,
                'category_id' => $this->job->category_id,
                'user_id' => $this->job->user_id,
                'created_at' => $this->job->created_at,
                'updated_at' => $this->job->updated_at,
            ],
        ];
    }

}
