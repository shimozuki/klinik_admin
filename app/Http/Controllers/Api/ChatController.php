<?php

namespace App\Http\Controllers\Api;

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ChMessage;
use Illuminate\Routing\Controller;
use App\Models\ChMessage as Message;
use App\Models\ChFavorite as Favorite;

class ChatController extends Controller
{
    public function contacts()
    {
        $userId = Auth::id();

        $contacts = ChMessage::join('users', function ($join) {
            $join->on('ch_messages.from_id', '=', 'users.id')
                ->orOn('ch_messages.to_id', '=', 'users.id');
        })
            ->where(function ($q) use ($userId) {
                $q->where('ch_messages.from_id', $userId)
                    ->orWhere('ch_messages.to_id', $userId);
            })
            ->where('users.id', '!=', $userId)
            ->select(
                'users.id',
                'users.name',
                'users.avatar',
                'users.active_status',
                DB::raw('MAX(ch_messages.created_at) as last_message_at'),
                DB::raw('SUM(
                    CASE 
                        WHEN ch_messages.to_id = ' . $userId . ' 
                        AND ch_messages.seen = 0 
                        THEN 1 ELSE 0 
                    END
                ) as unread')
            )
            ->groupBy(
                'users.id',
                'users.name',
                'users.avatar',
                'users.active_status'
            )
            ->orderByDesc('last_message_at')
            ->get();

        return response()->json([
            'contacts' => $contacts,
        ]);
    }

    public function fetchMessagesMobile(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $authId = Auth::id();
        $withId = $request->id;

        $messages = Message::where(function ($q) use ($authId, $withId) {
            $q->where('from_id', $authId)->where('to_id', $withId);
        })
            ->orWhere(function ($q) use ($authId, $withId) {
                $q->where('from_id', $withId)->where('to_id', $authId);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id'         => $msg->id,
                    'from_id'    => $msg->from_id,
                    'to_id'      => $msg->to_id,
                    'body'       => html_entity_decode($msg->body),
                    'seen'       => $msg->seen,
                    'created_at' => $msg->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'messages' => $messages,
        ], 200);
    }
}
