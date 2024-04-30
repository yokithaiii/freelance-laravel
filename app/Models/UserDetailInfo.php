<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDetailInfo extends Model
{
    use HasFactory;

    protected $table = 'users_detail_info';

    protected $fillable = [
        'user_id',
        'role_id',
        'name',
        'profession',
        'about_text',
        'contact_phone',
        'contact_telegram',
        'avatar',
        'jobs_count',
        'skills',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
