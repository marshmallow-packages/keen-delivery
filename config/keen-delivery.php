<?php

use Marshmallow\KeenDelivery\ParcelCarriers\DPD;
use Marshmallow\KeenDelivery\Http\Controllers\DownloadLabelController;
use Marshmallow\KeenDelivery\Http\Controllers\DownloadLabelsBulkController;

return [

    'api_path' => 'https://portal.keendelivery.com/api/v2',

    'api_token' => env('KEEN_DELIVERY_API_TOKEN', null),

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
