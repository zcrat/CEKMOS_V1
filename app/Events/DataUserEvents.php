<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class DataUserEvents implements ShouldBroadcastNow {

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $typedata;

    public function __construct(string $userId, string $typedata='none')
    {
        $this->userId = $userId;
        $this->typedata = $typedata;
    }   

    public function broadcastOn()
    { 
        return new PrivateChannel('Data.User.' . $this->userId);
    }

    public function broadcastAs()
    {
        return match ($this->typedata) {
            'delete', 'roles', 'permisos' => $this->typedata,
            default => 'update',
        };
    }

    public function broadcastWith()
    {
        $message = match ($this->typedata) {
            'delete' => 'Se Elimino Tu Usuario',
            'roles' => 'Se Actualizaron Los Roles De Tu Usuario',
            'permisos' => 'Se Actualizaron Los Permisos De Tu Usuario',
            default => 'Se Actualizo Tu Usuario',
        };

        return ['message' => $message];
    }

}
