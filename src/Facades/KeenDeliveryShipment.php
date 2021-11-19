<?php

namespace Marshmallow\KeenDelivery\Facades;

use Illuminate\Support\Facades\Facade;

class KeenDeliveryShipment extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Marshmallow\KeenDelivery\KeenDeliveryShipment::class;
    }
}
