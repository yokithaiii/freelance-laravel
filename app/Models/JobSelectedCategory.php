<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobSelectedCategory extends Model
{
    use HasFactory;

    protected $table = 'jobs_selected_categories';

    protected $fillable = [
        'job_id',
        'category_id',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

}
