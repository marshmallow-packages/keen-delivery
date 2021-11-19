<?php

namespace Marshmallow\KeenDelivery\Facades;

use Illuminate\Support\Facades\Facade;

class KeenDelivery extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Marshmallow\KeenDelivery\KeenDelivery::class;
    }
}
