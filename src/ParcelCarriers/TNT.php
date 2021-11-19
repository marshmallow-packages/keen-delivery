<?php

namespace Marshmallow\KeenDelivery\ParcelCarriers;

use Marshmallow\KeenDelivery\Contracts\ParcelCarriers;

class TNT implements ParcelCarriers
{
    public function getProduct(): string
    {
        return 'TNT';
    }

    public function getProductName(): string
    {
        return 'TNT';
    }

    public function services(): array
    {
        return [
            '48N' => __('Economy Express'),
        ];
    }
}
