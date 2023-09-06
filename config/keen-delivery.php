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
