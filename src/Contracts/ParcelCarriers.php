<?php

namespace Marshmallow\KeenDelivery\Contracts;

interface ParcelCarriers
{
    public function getProduct(): string;
    public function getProductName(): string;
    public function services(): array;
}
