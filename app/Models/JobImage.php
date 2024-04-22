<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobImage extends Model
{
    use HasFactory;

    protected $table = 'jobs_images';

    protected $fillable = [
        'file_name',
        'file_path',
        'job_id',
    ];
}
