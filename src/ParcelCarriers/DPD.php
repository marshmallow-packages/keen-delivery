<?php

namespace Marshmallow\KeenDelivery\ParcelCarriers;

use Marshmallow\KeenDelivery\Contracts\ParcelCarriers;

class DPD implements ParcelCarriers
{
    public function getProduct(): string
    {
        return 'DPD';
    }

    public function getProductName(): string
    {
        return 'DPD';
    }

    public function services(): array
    {
        return [
            'DPD_HOME_DROP_OFF' => __('Home (naar particulieren) - Inleveren'),
            'DPD_HOME_PICK_UP' => __('Home (naar particulieren) - Pick-up'),
            'DPD_CLASSIC_DROP_OFF' => __('Classic (naar bedrijven) - Inleveren'),
            'DPD_CLASSIC_PICK_UP' => __('Classic (naar bedrijven) - Pick-up'),
            'DPD_TO_SHOP_DROP_OFF' => __('2Shop (naar parcelshop) - Inleveren'),
            'DPD_TO_SHOP_PICK_UP' => __('2Shop (naar parcelshop) - Pick-up'),
            'DPD_10_PICK_UP' => __('DPD 10:00 (voor 10:00 leveren)'),
            'DPD_12_PICK_UP' => __('DPD 12:00 (voor 12:00 leveren)'),
            'DPD_18_PICK_UP' => __('DPD 18:00 (voor 18:00 leveren)'),
            'DPD_GUARANTEE_PICK_UP' => __('DPD Guarantee 18:00'),
            'DPD_RETURN_DROP_OFF' => __('Retour (klant levert in bij parcelshop)'),
            'DPD_RETURN_PICK_UP' => __('Retour (bij klant op laten halen)'),
        ];
    }
}
