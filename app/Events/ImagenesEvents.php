<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImagenesEvents implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $id;
    public string $type;
    public array $data;

    public function __construct(int $id, string $type, array $data)
    {
        $this->id = $id;
        $this->type = $type;
    }   

    public function broadcastOn()
    { 
        return new PrivateChannel('Imagenes');
    }

    public function broadcastAs()
    {
        $name='';
        switch($this->type){
            case 'Delete':
                $name='delete';
                break;
            default:
                $name='desconocido';
                break;
        } 
        return 'Events';
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