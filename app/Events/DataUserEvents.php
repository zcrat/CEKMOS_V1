<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DataUserEvents implements ShouldBroadcastNow
{
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
        return 'DataUserEvent';
    }

    public function broadcastWith()
    {
        if($this->typedata=='delete'){
            return ['message' => 'Se Elimino Tu Usuario','tipo'=> 58];
        }elseif($this->typedata=='roles'){
            return ['message' => 'Se Actualizaron Los Roles De Tu Usuario','tipo'=> 60];
        }elseif($this->typedata=='permisos'){
            return ['message' => 'Se Actualizaron Los Permisos De Tu Usuario','tipo'=> 61];
        }else{
            return ['message' => 'Se Actualizo Tu Usuario','tipo'=> 0];
        }
    }

}