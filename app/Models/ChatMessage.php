<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chats_messages';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
