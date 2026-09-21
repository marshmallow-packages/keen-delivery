<?php

namespace Marshmallow\KeenDelivery\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

class SendcloudException extends Exception
{
    public static function fromResponse(Response $response): self
    {
        $errors = collect($response->json('errors', []))
            ->map(fn ($error) => is_array($error) ? ($error['detail'] ?? $error['title'] ?? json_encode($error)) : $error)
            ->filter()
            ->implode(' ');

        $message = $errors ?: $response->body();

        return new self("Sendcloud returned HTTP {$response->status()}: {$message}", $response->status());
    }

    public static function announcementFailed(string $shipmentId, string $message): self
    {
        return new self("Sendcloud could not announce shipment {$shipmentId}: {$message}");
    }

    public static function unmappedService(string $service): self
    {
        return new self("No Sendcloud shipping option code is configured for service '{$service}'. Set it in keen-delivery.sendcloud.shipping_options.");
    }
}
