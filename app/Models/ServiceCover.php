<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCover extends Model
{
    use HasFactory;

    protected $table = 'services_covers';

    protected $fillable = [
        'file_name',
        'file_path',
        'service_id',
    ];

}
