<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceImage extends Model
{
    use HasFactory;

    protected $table = 'services_images';

    protected $fillable = [
        'file_name',
        'file_path',
        'service_id',
    ];

}
