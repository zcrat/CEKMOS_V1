<?php

use Illuminate\Support\Facades\Broadcast;

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
    return (int) $user->id === (int) $id;
});
Broadcast::channel('ordenes_servicio', function ($user) {
    return $user->canAny([
        'ver_presupuestos',
        'ver_recepciones_vehiculares',
    ]);
});
Broadcast::channel('importaciones.conceptos.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
