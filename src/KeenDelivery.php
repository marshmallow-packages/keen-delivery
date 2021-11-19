<?php

namespace Marshmallow\KeenDelivery;

use Error;
use Exception;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Stack;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Marshmallow\KeenDelivery\Models\Delivery;
use Marshmallow\KeenDelivery\Contracts\ParcelCarriers;

class KeenDelivery
{
    public static $deliveryModel = Delivery::class;

    public function getCarrier(string $carrier = null)
    {
        try {
            $carrier = $carrier ?? config('keen-delivery.default_carrier');
            $carrier = new $carrier;
        } catch (Error $e) {
            throw new Exception("No valid carrier was provided. The provided carrier is '{$carrier}'");
        }

        if (!class_implements($carrier, ParcelCarriers::class)) {
            throw new Exception(__(
                "This carrier doesn't implement the :interface interface",
                [
                    'interface' => ParcelCarriers::class,
                ]
            ));
        }

        return $carrier;
    }

    public function getBulkDownloadPath(Collection $models)
    {
        $bulk_data = base64_encode(json_encode([
            'class' => get_class($models->first()),
            'ids' => $models->pluck('id')->toArray(),
        ]));

        return URL::temporarySignedRoute(
            config('keen-delivery.routes.bulk_labels.name'),
            now()->addMinutes(
                config('keen-delivery.routes.bulk_labels.ttl')
            ),
            ['bulk_data' => $bulk_data]
        );
    }

    public function shipmentInfoField()
    {
        return Stack::make(__('Shipment info'), [
            Text::make(__('Track and Trace'))->resolveUsing(function ($value, $order) {
                return $order->getDeliveryTrackAndTraceNovaLink();
            })->asHtml(),
            Text::make(__('Label'), 'label_encoded')->resolveUsing(function ($value, $order) {
                if ($delivery = $order->getDeliverableWithLabel()) {
                    return '<a href="' . $delivery->downloadLabelRoute() . '">' .
                        __('Download Label') .
                        '</a>';
                }

                return __('n/a');
            })->asHtml(),
        ]);
    }
}
