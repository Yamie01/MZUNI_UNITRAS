<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VehicleLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trackableType;
    public $trackableId;
    public $location;

    public function __construct($trackableType, $trackableId, $location)
    {
        $this->trackableType = $trackableType;
        $this->trackableId = $trackableId;
        $this->location = $location;
    }

    public function broadcastOn()
    {
        return new Channel("tracking.{$this->trackableType}.{$this->trackableId}");
    }

    public function broadcastAs()
    {
        return 'location.updated';
    }
}