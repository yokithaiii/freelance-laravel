<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSelectedCategory extends Model
{
    use HasFactory;

    protected $table = 'jobs_selected_categories';

    protected $fillable = [
        'job_id',
        'category_id',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function category()
    {
        return $this->belongsTo(JobCategory::class);
    }

}
