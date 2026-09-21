<?php

namespace Marshmallow\KeenDelivery\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Marshmallow\KeenDelivery\Facades\KeenDeliveryApi;
use Marshmallow\KeenDelivery\Facades\SendyApi;
use Marshmallow\KeenDelivery\Facades\SendcloudApi;
use Marshmallow\KeenDelivery\Http\Controllers\Traits\FileDownload;

class DownloadLabelsBulkController extends Controller
{
    use FileDownload;

    private const SENDCLOUD_MAX_PARCELS = 20;

    public function __invoke($bulk_data)
    {
        $bulk_data = json_decode(base64_decode($bulk_data));
        $legacy = $bulk_data->legacy ?? false;
        $class = $bulk_data->class;
        $models = $class::whereIn('id', $bulk_data->ids)->get();

        $legacy_ids = [];
        $sendy_ids = [];
        $sendcloud_parcel_ids = [];

        foreach ($models as $model) {
            $deliverable_with_label = $model->getDeliverableWithLabel();

            if (! $deliverable_with_label) {
                continue;
            }

            if ($deliverable_with_label->is_sendcloud) {
                array_push($sendcloud_parcel_ids, ...$deliverable_with_label->getSendcloudParcelIds());
            } elseif ($deliverable_with_label->is_legacy) {
                $legacy_ids[] = $deliverable_with_label->carrier_shipping_id;
            } else {
                $sendy_ids[] = $deliverable_with_label->carrier_shipping_id;
            }
        }

        if ($legacy && count($legacy_ids) > 0) {
            return $this->getLegacyLabels($legacy_ids);
        }

        if (count($sendcloud_parcel_ids) > 0 && count($sendy_ids) === 0) {
            return $this->getSendcloudLabels($sendcloud_parcel_ids);
        }

        return $this->getSendyLabels($sendy_ids);
    }

    public function getSendcloudLabels(array $parcel_ids)
    {
        abort_if(
            count($parcel_ids) > self::SENDCLOUD_MAX_PARCELS,
            422,
            __('Sendcloud can merge at most :max labels at once. Select fewer orders.', ['max' => self::SENDCLOUD_MAX_PARCELS])
        );

        return $this->download(
            'shipping-labels.pdf',
            SendcloudApi::getLabels($parcel_ids),
        );
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
