<?php

namespace Marshmallow\KeenDelivery\Facades;

use Illuminate\Support\Facades\Facade;

class KeenDeliveryApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Marshmallow\KeenDelivery\KeenDeliveryApi::class;
    }
}
