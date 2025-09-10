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
    
        // Mark all messages from this user to admin as read
        Message::where('send_id', $userId)
            ->where('receiver_id', $authId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);
    
        // Fetch all messages between admin and user
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
            'is_read' => false,
        ]);

        return response()->json($message);
    }



    public function myChat()
    {
        if (Auth::user()->is_admin) {
            $users = User::where('is_admin', false)->get();
    
            // Get unread message count per user
            $users = $users->map(function($user) {
                $user->unread_count = \App\Models\Message::where('send_id', $user->id)
                    ->where('receiver_id', Auth::id())
                    ->where('is_read', 0)
                    ->count();
                return $user;
            });
    
            return view('assistance.admin', compact('users'));
        } else {
            $admin = User::where('is_admin', true)->first();
            return view('assistance.user', compact('admin'));
        }
    }


    // MessageController.php
    public function unreadCounts()
    {
        if (!Auth::user()->is_admin) return response()->json([]);
    
        $users = User::where('is_admin', false)->get();
    
        $users = $users->map(function($user) {
            $user->unread_count = Message::where('send_id', $user->id)
                ->where('receiver_id', Auth::id())
                ->where('is_read', 0)
                ->count();
            return $user;
        });
    
        return response()->json($users);
    }

    


    public function sendTest(Request $request)
    {
        $message = Message::create([
            'send_id' => $request->send_id ?? Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'is_read' => false,
        ]);
    
        return response()->json($message);
    }




}
