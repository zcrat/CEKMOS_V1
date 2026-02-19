<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Crypt;
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) Crypt::decrypt($id);
});
