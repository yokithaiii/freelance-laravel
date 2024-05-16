<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceSelectedCategory extends Model
{
    use HasFactory;

    protected $table = 'services_selected_categories';

    protected $fillable = [
        'service_id',
        'category_id',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }
}
