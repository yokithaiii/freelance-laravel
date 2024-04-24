<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOffer extends Model
{
    use HasFactory;

    protected $table = 'jobs_offers';

    protected $fillable = [
        'offer_description',
        'offer_price',
        'offer_date_deadline_days',
        'offer_status',
        'job_id',
        'customer_id',
        'executor_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'executor_id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
