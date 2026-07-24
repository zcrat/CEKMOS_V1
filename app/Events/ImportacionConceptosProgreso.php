<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportacionConceptosProgreso implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $usuarioId,
        public array $progreso
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("importaciones.conceptos.{$this->usuarioId}");
    }

    public function broadcastAs(): string
    {
        return 'progreso';
    }

    public function broadcastWith(): array
    {
        return $this->progreso;
    }
}
