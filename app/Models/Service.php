<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'service_title',
        'service_description',
        'service_needs',
        'service_price',
        'service_term_days',
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cover(): HasOne
    {
        return $this->hasOne(ServiceCover::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ServiceImage::class);
    }

}
