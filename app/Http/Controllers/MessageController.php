<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    //
    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => 'required',
            'message' => 'required',
        ]);




        $data['sender_id'] = auth()->id();

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('file');
            $data['file'] = $path;
        }
        // return response()->json($data, 201);
        try {
            $message = Message::create($data);

            broadcast(new MessageEvent($message))->toOthers();
            return response()->json($message, 201);
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage()], 400);

        }

    }
    public function messages(User $user)
    {
        try {
            $messages = Message::where(function ($query) use ($user) {
                $query->where('sender_id', auth()->id())
                    ->where('receiver_id', $user->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', auth()->id());
            })->get();

            return response()->json($messages, 201);
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage()], 400);
        }
    }


    public function users()
    {
        try {
            $users = User::where('id', '!=', auth()->id())->get();
            return response()->json(['users' => $users], 201);
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage()], 400);
        }
    }
}
