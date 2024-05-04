<?php

namespace App\Http\Controllers;

use App\Events\StoreMessageEvent;
use App\Http\Requests\MessageStoreRequest;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\ChatMessageStatus;
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
        $chats = ChatPivot::with('receiver', 'receiver.detailInfo', 'chat', 'chat.messages', 'chat.messages.status')
            ->where('sender_id', Auth::id())
            ->get();

        $chats->each(function ($chat) {
            $lastMessage = $chat->chat->messages->last();
            $chat->last_message = $lastMessage ? $lastMessage : [];
        });

        $user = User::find(Auth::id());

        return Inertia::render('Chat/Index', [
            'chats' => $chats,
            'user' => $user,
        ]);
    }

    public function show(string $login): JsonResponse
    {
        $user = User::where('login', $login)->first();

        $chat = ChatPivot::with('chat.messages', 'chat.messages.user.detailInfo', 'receiver.detailInfo', 'chat.messages.status')
            ->where('sender_id', Auth::id())
            ->where('receiver_id', $user->id)
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
            'chat_with_id' => $chat->receiver->id,
            'messages' => $formattedMessages,
        ];

        return response()->json($response, 200);
    }

    public function store(Request $request, User $user): array
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

        $messageStatus = new ChatMessageStatus([
            'chat_id' => $message->chat_id,
            'message_id' => $message->id,
            'is_read' => false,
        ]);

        $messageStatus->save();

        broadcast(new StoreMessageEvent($message, $user->id))->toOthers();

        return MessageResource::make($message)->resolve();
    }

    public function readMessage(Request $request): void
    {
        $request->validate([
            'chat_id' => 'required',
            'message_id' => 'required',
        ]);

        ChatMessageStatus::where('chat_id', $request->chat_id)
            ->where('message_id', $request->message_id)
            ->update(['is_read' => true]);
    }

    public function readAllMessages(Request $request)
    {
        $request->validate([
            'chat_id' => 'required',
        ]);

        $messages = ChatMessage::where('chat_id', $request->chat_id)->get();
        foreach ($messages as $message) {
            $messageStatus = ChatMessageStatus::where('message_id', $message->id)->first();
            if ($messageStatus) {
                $messageStatus->is_read = true;
                $messageStatus->save();
            }
        }
    }

}
