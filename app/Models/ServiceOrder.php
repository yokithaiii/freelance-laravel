<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    use HasFactory;

    protected $table = 'services_order';

    protected $fillable = [
        'message',
        'user_id',
        'service_id',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

}
