<?php

namespace Marshmallow\KeenDelivery\Events;

use Illuminate\Queue\SerializesModels;
use Marshmallow\KeenDelivery\Models\Delivery;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ShipmentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $delivery;

    public function __construct(Delivery $delivery)
    {
        $this->delivery = $delivery;
    }
}
