<?php

namespace Marshmallow\KeenDelivery\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Marshmallow\KeenDelivery\Facades\KeenDeliveryApi;
use Marshmallow\KeenDelivery\Http\Controllers\Traits\FileDownload;

class DownloadLabelsBulkController extends Controller
{
    use FileDownload;

    public function __invoke($bulk_data)
    {
        $bulk_data = json_decode(base64_decode($bulk_data));
        $class = $bulk_data->class;
        $models = $class::whereIn('id', $bulk_data->ids)->get();

        $delivery_ids = [];

        foreach ($models as $model) {
            $delivery_ids[] = $model->getDeliverableWithLabel()->carrier_shipping_id;
        }

        $response = KeenDeliveryApi::post('/label', [
            'shipments' => $delivery_ids,
        ]);

        if (array_key_exists('error', $response)) {
            throw new Exception($response['error'], 1);
        }

        /**
         * Create the contents of the PDF file.
         */
        $label_file_name = 'shipping-labels.pdf';

        return $this->download(
            $label_file_name,
            base64_decode($response['labels'])
        );
    }
}
