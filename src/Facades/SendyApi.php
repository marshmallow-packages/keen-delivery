<?php

namespace Marshmallow\KeenDelivery\Facades;

use Illuminate\Support\Facades\Facade;

class SendyApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Marshmallow\KeenDelivery\SendyApi::class;
    }
}
