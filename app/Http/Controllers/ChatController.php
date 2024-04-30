<?php

namespace App\Http\Controllers;

use App\Events\StoreMessageEvent;
use App\Http\Requests\MessageStoreRequest;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\ChatPivot;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    public function index(): Response
    {
        $chats = ChatPivot::with('receiver', 'receiver.detailInfo', 'chat', 'chat.messages')
            ->where('sender_id', Auth::id())
            ->get();

        $chats->each(function ($chat) {
            $lastMessage = $chat->chat->messages->last();
            $chat->last_message = $lastMessage;
        });

        return Inertia::render('Chat/Index', [
            'chats' => $chats,
        ]);
    }

    public function getMessages(string $chatId): JsonResponse
    {
        $chat = ChatPivot::with('chat.messages', 'chat.messages.user.detailInfo', 'receiver.detailInfo')
            ->where('chat_id', $chatId)
            ->where('receiver_id', '!=', Auth::id())
            ->first();

        if (!$chat) {
            return response()->json(['error' => 'Chat not found'], 404);
        }

        $formattedMessages = [];
        foreach ($chat->chat->messages as $message) {
            $formattedMessages[] = new MessageResource($message);
        }

        $response = [
            'id' => $chat->chat_id,
            'chat_with' => $chat->receiver->detailInfo['name'],
            'messages' => $formattedMessages,
        ];

        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'chatId' => 'required',
            'body' => 'string|max:255',
        ]);

        $message = new ChatMessage([
            'body' => $request->body,
            'user_id' => Auth::id(),
            'chat_id' => $request->chatId,
        ]);

        $message->save();

        broadcast(new StoreMessageEvent($message))->toOthers();

        return MessageResource::make($message)->resolve();
    }

}
