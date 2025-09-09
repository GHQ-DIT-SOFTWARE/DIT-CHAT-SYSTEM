<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Auth;

class MessageController extends Controller
{
    //
    public function index()
    {
        if (Auth::user()->is_admin) {
            $users = User::where('is_admin', false)->get();
            return view('chat.admin', compact('users'));
        } else {
            $admin = User::where('is_admin', true)->first();
            return view('chat.user', compact('admin'));
        }
    }

    public function getMessages($userId)
    {
        $authId = Auth::id();

        $messages = Message::where(function($q) use ($authId, $userId) {
                $q->where('send_id', $authId)->where('receiver_id', $userId);
            })
            ->orWhere(function($q) use ($authId, $userId) {
                $q->where('send_id', $userId)->where('receiver_id', $authId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function send(Request $request)
    {
        $message = Message::create([
            'send_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json($message);
    }
}
