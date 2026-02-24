<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Crypt;
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('Data.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('UsersEvents', function ($user) {
    return $user->can('ver usuarios');
});
Broadcast::channel('Data.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) Crypt::decrypt($id);
});
