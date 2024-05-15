<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessageImage extends Model
{
    use HasFactory;

    protected $table = 'chats_messages_images';

    protected $fillable = [
        'chat_id',
        'user_id',
        'message_id',
        'file_name',
        'file_path',
        'file_caption',
    ];

}
