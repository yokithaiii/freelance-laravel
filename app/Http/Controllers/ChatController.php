<?php

namespace App\Http\Controllers;

use App\Events\StoreMessageEvent;
use App\Http\Resources\ChatOfferResource;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\RoomResource;
use App\Models\ChatMessage;
use App\Models\ChatMessageImage;
use App\Models\ChatMessageStatus;
use App\Models\ChatPivot;
use App\Models\JobOffer;
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
        $rooms = ChatPivot::with('receiver.detailInfo', 'chat.messages')
            ->where('sender_id', Auth::id())
            ->get();

        $roomRes = [];
        foreach ($rooms as $room) {
            $roomRes[] = RoomResource::make($room)->resolve();
        }

        $user = User::find(Auth::id());

        return Inertia::render('Chat/Index', [
            'rooms' => $roomRes,
            'user' => $user,
        ]);
    }

    public function show(string $login): JsonResponse
    {
        $user = User::where('login', $login)->first();

        $chat = ChatPivot::with('chat.messages', 'chat.messages.image', 'chat.messages.user.detailInfo', 'receiver.detailInfo', 'chat.messages.status')
            ->where('sender_id', Auth::id())
            ->where('receiver_id', $user->id)
            ->first();

        if (!$chat) {
            return response()->json(['error' => 'Chat not found'], 404);
        }

        $formattedMessages = [];
        foreach ($chat->chat->messages as $message) {
            $formattedMessages[] = MessageResource::make($message)->resolve();
        }

        $offer = JobOffer::with('job')
            ->where(function($query) use ($user) {
                $query->where('customer_id', $user->id)
                    ->orWhere('executor_id', $user->id);
            })
            ->where(function($query) {
                $query->where('customer_id', Auth::id())
                    ->orWhere('executor_id', Auth::id());
            })
            ->first();

        $response = [
            'id' => $chat->chat_id,
            'chat_with' => $chat->receiver->detailInfo['name'],
            'chat_with_id' => $chat->receiver->id,
            'offer' => ChatOfferResource::make($offer)->resolve(),
            'messages' => $formattedMessages,
        ];

        return response()->json($response, 200);
    }

    public function store(Request $request, User $user): array|JsonResponse
    {
        $request->validate([
            'chatId' => 'required',
        ]);

        $message = new ChatMessage([
            'body' => $request->body,
            'user_id' => Auth::id(),
            'chat_id' => $request->chatId,
        ]);

        $message->save();

        if ($request->hasFile('files')) {
            $file = $request->file('files');

            $file_name = $file->getClientOriginalName();
            $file_path = $file->store('uploads', 'public');
            $messageImage = new ChatMessageImage([
                'chat_id' => $request->chatId,
                'message_id' => $message->id,
                'user_id' => Auth::id(),
                'file_name' => $file_name,
                'file_path' => $file_path,
                'file_caption' => $request->file_caption ? $request->file_caption : null,
            ]);
            $messageImage->save();
        }

        $messageStatus = new ChatMessageStatus([
            'chat_id' => $message->chat_id,
            'message_id' => $message->id,
            'is_read' => false,
        ]);

        $messageStatus->save();

        broadcast(new StoreMessageEvent($message, $user->id))->toOthers();

        return MessageResource::make($message)->resolve();
    }

}
