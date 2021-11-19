<?php

namespace Marshmallow\KeenDelivery\ParcelCarriers;

use Marshmallow\KeenDelivery\Contracts\ParcelCarriers;

class DHL implements ParcelCarriers
{
    public function getProduct(): string
    {
        return 'DHL';
    }

    public function getProductName(): string
    {
        return 'DHL Parcel';
    }

    public function services(): array
    {
        return [
            'DHL_FOR_YOU_MAILBOX_PACKAGE_PICK_UP' => __('DHL For You brievenbuspakket (naar particulieren) - Pick-up'),
            'DHL_FOR_YOU_MAILBOX_PACKAGE_DROP_OFF' => __('DHL For You brievenbuspakket (naar particulieren) - Inleveren parcelshop'),
            'DHL_EUROPLUS_PALLET' => __('Europlus Pallet (naar bedrijven)'),
            'DHL_EUROPLUS_PALLET_INTERNATIONAL' => __('Europlus Pallet Internationaal (naar bedrijven)'),
            'DHL_EUROPLUS_PALLET_RETURN' => __('Retour - Europlus Pallet (bij bedrijf op laten halen)'),
            'DHL_FOR_YOU_DROP_OFF_ADDRESS' => __('DHL For You (naar particulieren) - Inleveren parcelshop'),
            'DHL_EUROPLUS_DROP_OFF' => __('Europlus (naar bedrijven) - Inleveren parcelshop'),
            'DHL_PARCEL_CONNECT' => __('Parcel Connect (naar particulieren en bedrijven)'),
            'DHL_PARCEL_CONNECT_DROP_OFF' => __('Parcel Connect (naar particulieren en bedrijven)'),
            'DHL_FOR_YOU_DROP_OFF_RETURN' => __('Retour DHL For You (klant levert in bij Parcelshop)'),
            'DHL_FOR_YOU_PICK_UP_ADDRESS' => __('DHL For You (naar particulieren) - Pick-up'),
            'DHL_EUROPLUS_PICK_UP' => __('Europlus (naar bedrijven) - Pick-up'),
            'DHL_EUROPLUS_INTERNATIONAL_DROP_OFF' => __('Europlus Internationaal (naar particulieren en bedrijven)'),
            'DHL_EUROPLUS_INTERNATIONAL_PICK_UP' => __('Europlus Internationaal (naar particulieren en bedrijven)'),
            'DHL_FOR_YOU_PICK_UP_PARCELSHOP' => __('DHL For You 2Shop (naar Parcelshop)'),
            'DHL_FOR_YOU_DROP_OFF_PARCELSHOP' => __('DHL For You 2Shop (naar Parcelshop)'),
            'DHL_EXPRESSER' => __('Expresser (voor 11.00 uur leveren bij bedrijven)'),
            'DHL_FOR_YOU_PICK_UP_RETURN' => __('Retour DHL For You (bij particulier op laten halen)'),
            'DHL_EUROPLUS_RETURN' => __('Retour Europlus (bij bedrijf op laten halen)'),
        ];
    }
}
