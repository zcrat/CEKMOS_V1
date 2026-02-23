<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UsersEvents implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $typedata;

    public function __construct(int $userId, string $typedata)
    {
        $this->userId = $userId;
        $this->typedata = $typedata;
    }   

    public function broadcastOn()
    { 
        return new PrivateChannel('UsersEvents');
    }

    public function broadcastAs()
    {
        return 'Events';
    }

    public function broadcastWith()
    {
        if($this->typedata=='delete'){
            return ['message' => 'Se Elimino Tu Usuario','tipo'=> 58, 'id_user'=>$this->userId];
        }elseif($this->typedata=='roles'){
            return ['message' => 'Se Actualizaron Los Roles De Tu Usuario','tipo'=> 60, 'id_user'=>$this->userId];
        }elseif($this->typedata=='permisos'){
            return ['message' => 'Se Actualizaron Los Permisos De Tu Usuario','tipo'=> 61, 'id_user'=>$this->userId];
        }else{
            return ['message' => 'Se Actualizo Tu Usuario','tipo'=> 59, 'id_user'=>$this->userId];
        }
    }

}