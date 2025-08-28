<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Notificaciones;

class SeendNotification extends Notification implements ShouldBroadcast 
{
    use Queueable;

    protected $user;
    protected $notificacion;
    public function __construct($user_id,$notificacion)
    {
        $this->user = $user_id;
        $this->notificacion = $notificacion;
    }

    public function via(object $notifiable): array
    {
        return ['broadcast'];
    }
    public function broadcastAs(): string
    {
        return 'NewNotifications';
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'notificacion' => $this->notificacion,
        ]);
    }
}
