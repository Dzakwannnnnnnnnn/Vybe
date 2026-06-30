<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Show direct message inbox and active chat history.
     */
    public function index(?User $activeUser = null)
    {
        $userId = Auth::id();

        // 1. Find all user IDs we have messages with
        $senderIds = Message::where('receiver_id', $userId)->pluck('sender_id')->toArray();
        $receiverIds = Message::where('sender_id', $userId)->pluck('receiver_id')->toArray();
        $chatUserIds = array_unique(array_merge($senderIds, $receiverIds));

        // Get profiles of those users
        $chats = User::whereIn('id', $chatUserIds)
            ->where('id', '!=', $userId)
            ->get()
            ->map(function ($chatUser) use ($userId) {
                // Find last message
                $lastMessage = Message::where(function ($q) use ($userId, $chatUser) {
                    $q->where('sender_id', $userId)->where('receiver_id', $chatUser->id);
                })->orWhere(function ($q) use ($userId, $chatUser) {
                    $q->where('sender_id', $chatUser->id)->where('receiver_id', $userId);
                })->latest()->first();

                // Find count of unread messages from this user
                $unreadCount = Message::where('sender_id', $chatUser->id)
                    ->where('receiver_id', $userId)
                    ->whereNull('read_at')
                    ->count();

                $chatUser->last_message = $lastMessage;
                $chatUser->unread_count = $unreadCount;
                return $chatUser;
            })
            ->sortByDesc(function ($u) {
                return $u->last_message ? $u->last_message->created_at : now()->subYear();
            })
            ->values();

        $messages = collect();
        if ($activeUser) {
            // Eager load messages between them
            $messages = Message::where(function ($q) use ($userId, $activeUser) {
                $q->where('sender_id', $userId)->where('receiver_id', $activeUser->id);
            })->orWhere(function ($q) use ($userId, $activeUser) {
                $q->where('sender_id', $activeUser->id)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

            // Mark received messages from this user as read
            Message::where('sender_id', $activeUser->id)
                ->where('receiver_id', $userId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return view('messages.index', compact('chats', 'activeUser', 'messages'));
    }

    /**
     * Store a new direct message.
     */
    public function store(Request $request, User $user)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'content' => $request->content,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()->route('messages.show', $user->id);
    }

    /**
     * Fetch new messages since a given message ID (for polling).
     */
    public function fetchNew(User $user, Request $request)
    {
        $userId = Auth::id();
        $lastId = $request->query('last_id', 0);

        // Fetch messages between these two users that are newer than the last message ID
        $newMessages = Message::where('id', '>', $lastId)
            ->where(function ($q) use ($userId, $user) {
                $q->where('sender_id', $userId)->where('receiver_id', $user->id);
            })->orWhere(function ($q) use ($userId, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark any received messages as read
        Message::where('id', '>', $lastId)
            ->where('sender_id', $user->id)
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'messages' => $newMessages
        ]);
    }
}
