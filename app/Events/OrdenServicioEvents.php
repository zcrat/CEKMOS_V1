<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrdenServicioEvents implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $id;
    public string $type;
    public array $data;

    public function __construct(int $id, string $type, array $data=[])
    {
        $this->id = $id;
        $this->type = $type;
        $this->data = $data;
    }   

    public function broadcastOn()
    { 
        return new PrivateChannel('ordenes_servicio');
    }

    public function broadcastAs()
    {
        $name=$this->type;
        switch($name){
            case 'update':
            case 'create':
            case 'delete':
                break;
            default:
                $name='desconocido';
                break;
        } 
        return $name;
    }

    public function broadcastWith()
    {
        $return = [
            'id'=>$this->id
        ];

        foreach ($this->data as $key => $value){
                 $return[$key] = $value;
        }

        return $return;
    }

}