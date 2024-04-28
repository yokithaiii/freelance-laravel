<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatPivot;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{

    public function index()
    {
        $chats = ChatPivot::with('receiver', 'receiver.detailInfo', 'chat')
            ->where('sender_id', Auth::id())
            ->get();

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
            $formattedMessages[] = [
                'id' => $message->id,
                'body' => $message->body,
                'user' => [
                    'id' => $message->user['id'],
                    'name' => $message->user->detailInfo['name'],
                    'avatar' => $message->user->detailInfo['avatar'],
                ],
            ];
        }

        $response = [
            'id' => $chat->id,
            'chat_with' => $chat->receiver->detailInfo['name'],
            'messages' => $formattedMessages,
        ];

        return response()->json($response, 200);
    }

//    public function create($login): Response|JsonResponse
//    {
        //sender - отправитель (вы)
        //receiver - получатель

        // $sender = User::with('detailInfo')->find(Auth::id());
        // if (!$sender) {
        //     return response()->json(['error' => 'user not found'], 404);
        // }

        // $receiver = User::where('login', $login)->with('detailInfo')->first();
        // if (!$receiver) {
        //     return response()->json(['error' => 'user not found'], 404);
        // }

        // return Inertia::render('Chat/Create', [
        //     'sender' => $sender,
        //     'receiver' => $receiver,
        // ]);
//    }

    public function store(Request $request)
    {
        // Валидация данных
        $request->validate([
            'body' => 'string|max:255',
        ]);

        // Создание нового чата
        $chat = Chat::updateOrCreate();

        // Присоединение пользователей к чату
        $users = User::find($request->users);
        $chat->users()->attach($users);

        // Добавление текущего пользователя в чат
        $chat->users()->attach(Auth::user());

        return response()->json(['message' => 'Chat created successfully', 'chat' => $chat], 201);
    }

}
