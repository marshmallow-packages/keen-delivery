<?php

namespace Marshmallow\KeenDelivery\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Marshmallow\KeenDelivery\Facades\KeenDeliveryApi;
use Marshmallow\KeenDelivery\Facades\SendyApi;
use Marshmallow\KeenDelivery\Http\Controllers\Traits\FileDownload;

class DownloadLabelsBulkController extends Controller
{
    use FileDownload;

    public function __invoke($bulk_data)
    {
        $bulk_data = json_decode(base64_decode($bulk_data));
        $legacy = $bulk_data->legacy ?? false;
        $class = $bulk_data->class;
        $models = $class::whereIn('id', $bulk_data->ids)->get();

        $delivery_ids = [];

        foreach ($models as $model) {
            $delivery_ids[] = $model->getDeliverableWithLabel()->carrier_shipping_id;
        }

        $legacy_ids = [];
        $sendy_ids = [];

        collect($delivery_ids)->each(function ($delivery_id) use (&$legacy_ids, &$sendy_ids) {
            if (is_numeric($delivery_id)) {
                $legacy_ids[] = $delivery_id;
            } else {
                $sendy_ids[] = $delivery_id;
            }
        });

        if ($legacy && count($legacy_ids) > 0) {
            return $this->getLegacyLabels($legacy_ids);
        }

        return $this->getSendyLabels($sendy_ids);
    }

    public function getLegacyLabels($delivery_ids)
    {
        $response = KeenDeliveryApi::post('/label', [
            'shipments' => $delivery_ids,
        ]);

        if (array_key_exists('error', $response)) {
            throw new Exception($response['error'], 1);
        }

        $decoded_labels = base64_decode($response['labels']);

        /**
         * Create the contents of the PDF file.
         */
        $label_file_name = 'shipping-labels.pdf';

        return $this->download(
            $label_file_name,
            $decoded_labels,
        );
    }

    public function getSendyLabels($delivery_ids)
    {
        $response = SendyApi::getLabels($delivery_ids);
        $decoded_labels = base64_decode($response['labels']);

        /**
         * Create the contents of the PDF file.
         */
        $label_file_name = 'shipping-labels.pdf';

        return $this->download(
            $label_file_name,
            $decoded_labels,
        );
    }
}
