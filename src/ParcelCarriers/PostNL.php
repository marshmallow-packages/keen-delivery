<?php

namespace Marshmallow\KeenDelivery\ParcelCarriers;

use Marshmallow\KeenDelivery\Contracts\ParcelCarriers;

class PostNL implements ParcelCarriers
{
    public function getProduct(): string
    {
        return 'PostNL';
    }

    public function getProductName(): string
    {
        return 'PostNL';
    }

    public function services(): array
    {
        return [
            'POSTNL_MP' => __('Brievenbuspakje+'),
            'DOMESTIC_PACKAGE' => __('Pakketten NL (naar particulieren & bedrijven)'),
            'DOMESTIC_PACKAGE_M' => __('Pakketten NL (naar particulieren & bedrijven) - M'),
            'DOMESTIC_PACKAGE_L' => __('Pakketten NL (naar particulieren & bedrijven) - L'),
            'DOMESTIC_PACKAGE_XL' => __('Pakketten NL (naar particulieren & bedrijven) - XL'),
            'TO_SHOP' => __('PakjeGemak (naar PostNL locatie)'),
            'TO_SHOP_M' => __('PakjeGemak (naar PostNL locatie) - M'),
            'TO_SHOP_L' => __('PakjeGemak (naar PostNL locatie) - L'),
            'TO_SHOP_XL' => __('PakjeGemak (naar PostNL locatie) - XL'),
            'GLOBAL_PACK' => __('Pakketten Non-EU (naar particulieren & bedrijven)'),
            'EPS_PACKAGE_CONSUMER' => __('Pakketten EU (naar particulieren)'),
            'EPS_PACKAGE_BUSINESS' => __('Pakketten EU (naar bedrijven)'),
            'DOMESTIC_PACKAGE_RETURN' => __('Retourpakketten NL (klant levert in bij PostNL locatie)'),
            'DOMESTIC_PACKAGE_RETURN_M' => __('Retourpakketten NL (klant levert in bij PostNL locatie) - M'),
            'DOMESTIC_PACKAGE_RETURN_L' => __('Retourpakketten NL (klant levert in bij PostNL locatie) - L'),
            'DOMESTIC_PACKAGE_RETURN_XL' => __('Retourpakketten NL (klant levert in bij PostNL locatie) - Xl'),
        ];
    }
}
