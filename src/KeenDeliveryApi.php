<?php

namespace Marshmallow\KeenDelivery;

use Illuminate\Support\Facades\Http;
use Marshmallow\KeenDelivery\Events\ShipmentCreated;

class KeenDeliveryApi
{
    public function get($path, $data = [])
    {
        $resposne = Http::get(config('keen-delivery.api_path') . $path, array_merge([
            'api_token' => config('keen-delivery.api_token')
        ], $data));

        return $resposne->json();
    }

    public function post($path, $data)
    {
        $path = config('keen-delivery.api_path') . $path;
        $path .= '?api_token=' . config('keen-delivery.api_token');

        $resposne = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json; charset=utf-8',
        ])->post($path, $data);

        return $resposne->json();
    }

    public function verifyApiToken()
    {
        return $this->get('/authorization');
    }

    public function listShippingMethods()
    {
        return $this->get('/shipping_methods');
    }

    public function createShipment(KeenDeliveryShipment $shipment)
    {
        $response = $this->post('/shipment', $shipment->toLegacyArray());

        $model = $shipment->createDeliveryableRecord();

        $response_successfull = array_key_exists('shipment_id', $response);

        if ($response_successfull) {
            foreach ($response['track_and_trace'] as $track_and_trace => $track_and_trace_url) {
                break;
            }

            $model->update([
                'carrier_shipping_id' => $response['shipment_id'],
                'track_and_trace_id' => $track_and_trace,
                'track_and_trace_url' => $track_and_trace_url,
                'label_encoded' => $response['label'],
            ]);
        }

        $model->update([
            'response' => $response,
        ]);

        event(
            new ShipmentCreated(
                $model->fresh()
            )
        );
    }
}
