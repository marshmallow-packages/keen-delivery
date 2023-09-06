<?php

namespace Marshmallow\KeenDelivery;

use Illuminate\Support\Facades\Http;
use Marshmallow\KeenDelivery\Events\ShipmentCreated;
use Illuminate\Support\Arr;

class SendyApi
{
    protected $client;

    public function __construct()
    {
        $sendyToken = config('keen-delivery.sendy_token');
        $this->client = new \Sendy\Api\Connection();
        $this->client->setAccessToken($sendyToken);
    }

    public function me()
    {
        return $this->client->me->get();
    }

    public function getShopUuid()
    {
        $shops = $this->listShops();
        $shop = collect($shops)->first();
        return Arr::get($shop, 'uuid');
    }

    public function listShops()
    {
        return $this->client->shop->list();
    }

    public function listShipments()
    {
        return $this->client->shipment->list();
    }

    public function listCarriers()
    {
        return $this->client->carrier->list();
    }

    public function listServices($carrierId)
    {
        return $this->client->service->list($carrierId);
    }

    public function listShippingPreferences()
    {
        return $this->client->shippingpreference->list();
    }

    public function getShippingPreference()
    {
        $preferences = $this->listShippingPreferences();
        $preference = collect($preferences)->first();
        return Arr::get($preference, 'uuid');
    }

    public function listShippingMethods()
    {
        $carriers = $this->listCarriers();
        $carrier_services = collect($carriers)->map(function ($carrier) {
            $carrierId = Arr::get($carrier, 'id');
            $services = $this->listServices($carrierId);
            $carrier['services'] = $services;
            return $carrier;
        });

        return $carrier_services;
    }

    public function getShipment($shipmentId)
    {
        return $this->client->shipment->get($shipmentId);
    }

    public function deleteShipment($shipmentId)
    {
        return $this->client->shipment->delete($shipmentId);
    }

    public function generateShipment($shipmentId, $asynchronous = false)
    {
        return $this->client->shipment->generate($shipmentId, $asynchronous);
    }

    public function postShipment($shipmentData)
    {
        return $this->client->post('/shipments', $shipmentData);
    }

    public function getLabel($shipmentId)
    {
        return $this->client->shipment->labels($shipmentId);
    }

    public function createShipmentFromPreference(array $shipmentData, $preferenceId, $generateDirectly = false)
    {
        collect([
            'carrier',
            'service',
            'options',
        ])->each(function ($key) use (&$shipmentData) {
            Arr::pull($shipmentData, $key);
        });
        $shipmentData = Arr::prepend($shipmentData, $preferenceId, 'preference_id');

        return $this->client->shipment->createFromPreference($shipmentData, $generateDirectly);
    }

    public function createShipment(KeenDeliveryShipment $shipment)
    {
        $shipmentData = $shipment->toArray();

        $shipmentResponse = $this->postShipment($shipmentData);
        $shipmentId = Arr::get($shipmentResponse, 'uuid');

        $this->generateShipment($shipmentId);

        $model = $shipment->createDeliveryableRecord();

        $packages = Arr::get($shipmentResponse, 'packages');

        if (empty($packages)) {
            $shipmentResponse = $this->getShipment($shipmentId);
            $packages = Arr::get($shipmentResponse, 'packages');
        }


        if ($shipmentId && $packages) {
            $package = collect($packages)->first();

            $model->update([
                'carrier_shipping_id' => $shipmentId,
                'track_and_trace_id' => Arr::get($package, 'package_number'),
                'track_and_trace_url' => Arr::get($package, 'tracking_url'),
                'label_encoded' =>  $label ?? null,
            ]);
        }

        $model->update([
            'response' => $shipmentResponse,
        ]);

        event(
            new ShipmentCreated(
                $model->fresh()
            )
        );
    }
}
