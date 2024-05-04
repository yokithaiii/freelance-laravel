<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessageStatus extends Model
{
    use HasFactory;

    protected $table = 'chats_messages_status';

    protected $fillable = [
        'chat_id',
        'message_id',
        'is_read'
    ];

}
