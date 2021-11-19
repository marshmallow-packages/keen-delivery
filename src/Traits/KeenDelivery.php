<?php

namespace Marshmallow\KeenDelivery\Traits;

use Illuminate\Database\Eloquent\Builder;
use Marshmallow\KeenDelivery\Contracts\ParcelCarriers;
use Marshmallow\KeenDelivery\Facades\KeenDeliveryShipment;
use Marshmallow\KeenDelivery\KeenDelivery as BaseKeenDelivery;

trait KeenDelivery
{
    public function createShipment()
    {
        return KeenDeliveryShipment::of($this)
            ->product(
                $this->getKeenDeliverShipmentProduct()
            )
            ->service(
                $this->getKeenDeliveryShipmentService()
            )
            ->amount(
                $this->getKeenDeliveryPackageAmount()
            )
            ->reference(
                $this->getDeliveryReference()
            )
            ->companyName(
                $this->getDeliveryCompanyName()
            )
            ->contactPerson(
                $this->getDeliveryContactPerson()
            )
            ->street(
                $this->getDeliveryStreet()
            )
            ->number(
                $this->getDeliveryNumber()
            )
            ->numberAddition(
                $this->getDeliveryAddition()
            )
            ->zipCode(
                $this->getDeliveryZipCode()
            )
            ->city(
                $this->getDeliveryCity()
            )
            ->country(
                $this->getDeliveryCountry()
            )
            ->phone(
                $this->getDeliveryPhone()
            )
            ->email(
                $this->getDeliveryEmail()
            )
            ->comment(
                $this->getDeliveryComment()
            )
            ->weight(
                $this->getDeliveryWeight()
            )
            ->customData(
                $this->getCustomDeliveryData()
            )
            ->create();
    }

    protected function getKeenDeliverShipmentProduct(): ParcelCarriers
    {
        $carrier = config('keen-delivery.default_carrier');
        return new $carrier;
    }

    protected function getKeenDeliveryShipmentService(): string
    {
        return config('keen-delivery.default_carrier_service');
    }

    protected function getKeenDeliveryPackageAmount(): int
    {
        return 1;
    }

    protected function getDeliveryTrackAndTraceId()
    {
        return $this->getDeliverableWithLabel()?->track_and_trace_id;
    }

    protected function getDeliveryTrackAndTraceUrl()
    {
        return $this->getDeliverableWithLabel()?->track_and_trace_url;
    }

    public function getDeliverableWithLabel()
    {
        return $this->deliverable()->whereHasLabel()->first();
    }

    public function getDeliveryTrackAndTraceNovaLink()
    {
        $track_and_trace_id = $this->getDeliveryTrackAndTraceId();
        $track_and_trace_url = $this->getDeliveryTrackAndTraceUrl();

        if ($track_and_trace_id && $track_and_trace_url) {
            return '<a href="' . $track_and_trace_url . '" target="_blank">' . $track_and_trace_id . '</a>';
        }

        $does_have_deliverable = $this->deliverable->count() > 0;

        if ($does_have_deliverable) {
            return __('Has errors');
        }

        return __('n/a');
    }

    public function scopeShipmentSubmitted(Builder $builder)
    {
        $builder->has('deliverable', '>=', 1, 'and', function ($builder) {
            $builder->whereHasLabel();
        });
    }

    public function scopeShipmentNotSubmitted(Builder $builder)
    {
        $builder->doesntHave('deliverable')
            ->orWhereHas('deliverable', function ($builder) {
                $builder->whereHasLabel();
            }, '=', 0);
    }

    public function deliverable()
    {
        return $this->morphMany(BaseKeenDelivery::$deliveryModel, 'deliverable');
    }

    abstract public function getDeliveryReference(): string;
    abstract public function getDeliveryCompanyName(): ?string;
    abstract public function getDeliveryContactPerson(): ?string;
    abstract public function getDeliveryStreet(): string;
    abstract public function getDeliveryNumber(): string;
    abstract public function getDeliveryAddition(): ?string;
    abstract public function getDeliveryZipCode(): string;
    abstract public function getDeliveryCity(): string;
    abstract public function getDeliveryCountry(): string;
    abstract public function getDeliveryPhone(): ?string;
    abstract public function getDeliveryEmail(): ?string;
    abstract public function getDeliveryComment(): ?string;
    abstract public function getDeliveryWeight(): ?int;
    abstract public function getCustomDeliveryData(): array;
}
