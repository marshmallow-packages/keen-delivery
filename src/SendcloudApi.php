<?php

namespace Marshmallow\KeenDelivery;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Marshmallow\KeenDelivery\Events\ShipmentCreated;
use Marshmallow\KeenDelivery\Exceptions\SendcloudException;

class SendcloudApi
{
    public function listSenderAddresses(): array
    {
        return $this->json($this->client()->get('v2/user/addresses/sender'), 'sender_addresses');
    }

    public function listShippingOptions(array $filters = []): array
    {
        return $this->json($this->client()->post('v3/shipping-options', (object) $filters), 'data');
    }

    public function announceShipment(array $shipmentData): array
    {
        return $this->json($this->client()->post('v3/shipments/announce', $shipmentData), 'data');
    }

    /** @param array<int, int> $parcelIds */
    public function getLabels(array $parcelIds): string
    {
        $query = collect($parcelIds)
            ->map(fn (int $parcelId) => "parcels={$parcelId}")
            ->implode('&');

        $response = $this->client()
            ->accept('application/pdf')
            ->get("v3/parcel-documents/label?{$query}");

        if ($response->failed()) {
            throw SendcloudException::fromResponse($response);
        }

        return $response->body();
    }

    public function createShipment(KeenDeliveryShipment $shipment): void
    {
        $shipmentData = $shipment->toSendcloudArray();

        $shipmentResponse = $this->announceShipment($shipmentData);
        $shipmentId = Arr::get($shipmentResponse, 'id');
        $parcels = collect(Arr::get($shipmentResponse, 'parcels', []));

        $model = $shipment->createDeliveryableRecord();
        $model->update([
            'response' => $shipmentResponse,
        ]);

        $failedParcel = $parcels->first(
            fn (array $parcel) => Arr::get($parcel, 'status.code') !== 'READY_TO_SEND'
        );

        if ($parcels->isEmpty() || $failedParcel) {
            throw SendcloudException::announcementFailed(
                $shipmentId ?? 'unknown',
                Arr::get($failedParcel, 'status.message', 'no parcels were announced'),
            );
        }

        $firstParcel = $parcels->first();
        $trackingNumber = Arr::get($firstParcel, 'tracking_number');

        $model->update([
            'carrier_shipping_id' => $shipmentId,
            'track_and_trace_id' => $trackingNumber,
            'track_and_trace_url' => Arr::get($firstParcel, 'tracking_url')
                ?? $this->fallbackTrackingUrl($shipmentResponse, $trackingNumber),
            'label_encoded' => $this->getEncodedLabel($parcels->all()),
        ]);

        event(
            new ShipmentCreated(
                $model->fresh()
            )
        );
    }

    protected function getEncodedLabel(array $parcels): string
    {
        if (count($parcels) === 1) {
            if ($labelFile = Arr::get($parcels[0], 'label_file')) {
                return $labelFile;
            }
        }

        $parcelIds = collect($parcels)->pluck('id')->map(fn ($parcelId) => (int) $parcelId)->all();

        return base64_encode($this->getLabels($parcelIds));
    }

    /**
     * Sendcloud may leave tracking_url empty right after announcing, so build
     * the public PostNL track & trace link ourselves in that case.
     */
    protected function fallbackTrackingUrl(array $shipmentResponse, ?string $trackingNumber): ?string
    {
        if (! $trackingNumber) {
            return null;
        }

        if (Arr::get($shipmentResponse, 'carrier.code') !== 'postnl') {
            return null;
        }

        $country = Arr::get($shipmentResponse, 'to_address.country_code');
        $postalCode = str_replace(' ', '', (string) Arr::get($shipmentResponse, 'to_address.postal_code'));

        return "https://jouw.postnl.nl/track-and-trace/{$trackingNumber}-{$country}-{$postalCode}";
    }

    protected function client(): PendingRequest
    {
        return Http::baseUrl(config('keen-delivery.sendcloud.api_path'))
            ->withBasicAuth(
                (string) config('keen-delivery.sendcloud.public_key'),
                (string) config('keen-delivery.sendcloud.secret_key'),
            )
            ->acceptJson()
            ->timeout(30);
    }

    protected function json(Response $response, string $key): array
    {
        if ($response->failed()) {
            throw SendcloudException::fromResponse($response);
        }

        return $response->json($key, []);
    }
}
