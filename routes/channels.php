<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{senderId}.{receiverId}', function ($user, $senderId, $receiverId) {
    Log::info('Channel Authorization Check', [
        'user_id' => $user->id,
        'sender_id' => $senderId,
        'receiver_id' => $receiverId,
        'authorized' => (int) $user->id === (int) $senderId || (int) $user->id === (int) $receiverId,
    ]);

    return (int) $user->id === (int) $senderId || (int) $user->id === (int) $receiverId;
});



// Broadcast::channel('private-chat.{senderId}.{receiverId}', function ($user, $senderId, $receiverId) {
//     // return (int) $user->id === (int) $user->id;
//     return (int) $user->id === (int) $senderId || (int) $user->id === (int) $receiverId;
// });
