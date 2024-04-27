<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    public function create($login): Response|JsonResponse
    {
        //sender - отправитель (вы)
        //receiver - получатель

        $sender = User::with('detailInfo')->find(Auth::id());
        if (!$sender) {
            return response()->json(['error' => 'user not found'], 404);
        }

        $receiver = User::where('login', $login)->with('detailInfo')->first();
        if (!$receiver) {
            return response()->json(['error' => 'user not found'], 404);
        }

        return Inertia::render('Chat/Create', [
            'sender' => $sender,
            'receiver' => $receiver,
        ]);
    }

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
