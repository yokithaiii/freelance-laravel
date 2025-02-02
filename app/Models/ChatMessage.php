<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chats_messages';

    protected $fillable = [
        'body',
        'user_id',
        'chat_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function status()
    {
        return $this->hasOne(ChatMessageStatus::class, 'message_id');
    }

    public function image()
    {
        return $this->hasOne(ChatMessageImage::class, 'message_id');
    }
}
