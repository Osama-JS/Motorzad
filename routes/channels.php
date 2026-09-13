<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Auction public/presence channel (allows authenticated users to announce presence if needed)
Broadcast::channel('auction.{id}', function ($user, $id) {
    return $user ? [
        'id' => $user->id,
        'name' => "مزايد #{$user->id}"
    ] : true;
});

