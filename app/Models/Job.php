<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'price_in_hour_flag',
        'date_deadline',
        'category_id',
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function selectedCategory(): HasMany
    {
        return $this->hasMany(JobSelectedCategory::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(JobImage::class);
    }
}
