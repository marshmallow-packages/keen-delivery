<?php

namespace Marshmallow\KeenDelivery\Facades;

use Illuminate\Support\Facades\Facade;

class SendcloudApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Marshmallow\KeenDelivery\SendcloudApi::class;
    }
}
