<?php

use Marshmallow\KeenDelivery\ParcelCarriers\DPD;
use Marshmallow\KeenDelivery\Http\Controllers\DownloadLabelController;
use Marshmallow\KeenDelivery\Http\Controllers\DownloadLabelsBulkController;

return [

    'api_path' => 'https://portal.keendelivery.com/api/v2',

    'api_token' => env('KEEN_DELIVERY_API_TOKEN', null),

    'use_legacy' => env('KEEN_DELIVERY_LEGACY_ENABLED', false),
    'sendy_token' => env('SENDY_ACCESS_TOKEN', null),
    'sendy_shop_id' => env('SENDY_SHOP_ID', null),

    /*
     * Which API new shipments are created with: 'keen', 'sendy' or 'sendcloud'.
     * When empty, use_legacy decides between 'keen' and 'sendy'.
     */
    'driver' => env('KEEN_DELIVERY_DRIVER'),

    'sendcloud' => [
        'api_path' => 'https://panel.sendcloud.sc/api',
        'public_key' => env('SENDCLOUD_PUBLIC_KEY'),
        'secret_key' => env('SENDCLOUD_SECRET_KEY'),
        'sender_address_id' => env('SENDCLOUD_SENDER_ADDRESS_ID'),
        'contract_id' => env('SENDCLOUD_CONTRACT_ID'),

        /*
         * Maps the service stored on a shipment to a Sendcloud shipping option code.
         * Run `php artisan keen-delivery:sendcloud-setup` to list the codes of your account.
         */
        'shipping_options' => [
            // 'DOMESTIC_PACKAGE' => 'postnl:standard',
        ],
    ],

    'default_carrier' => DPD::class,

    'default_carrier_service' => 'DPD_HOME_PICK_UP',

    'delivery_models' => [
        \App\Nova\Order::class,
    ],

    'routes' => [
        'single_label' => [
            'path' => '/marshmallow/delivery/download/label/{delivery}',
            'name' => 'delivery.download.label',
            'controller' => DownloadLabelController::class,
            'ttl' => 1,
        ],
        'bulk_labels' => [
            'path' => '/marshmallow/delivery/download/labels/{bulk_data}',
            'name' => 'delivery.download.labels.bulk',
            'controller' => DownloadLabelsBulkController::class,
            'ttl' => 1,
        ],
    ],
];
