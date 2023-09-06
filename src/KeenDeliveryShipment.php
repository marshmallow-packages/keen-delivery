<?php

namespace Marshmallow\KeenDelivery;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Marshmallow\KeenDelivery\KeenDelivery;
use Marshmallow\KeenDelivery\Facades\KeenDeliveryApi;
use Marshmallow\KeenDelivery\Facades\SendyApi;
use Marshmallow\KeenDelivery\Contracts\ParcelCarriers;

class KeenDeliveryShipment
{
    protected $product = null;

    protected $service = null;

    protected $amount = null;

    protected $reference = null;

    protected $company_name = null;

    protected $contact_person = null;

    protected $street_line_1 = null;

    protected $number_line_1 = null;

    protected $number_line_1_addition = null;

    protected $zip_code = null;

    protected $city = null;

    protected $country = null;

    protected $phone = null;

    protected $email = null;

    protected $comment = null;

    protected $weight = null;

    protected $custom_data = [];

    protected $deliverable;

    public function toArray(): array
    {
        $shop_id = config('keen-delivery.sendy_shop_id');
        $data = [
            'carrier' => $this->product,
            'service' => $this->service,
            'shop_id' => $shop_id,
            'company_name' => $this->company_name,
            'contact' => $this->contact_person,
            'street' => $this->street_line_1,
            'number' => $this->number_line_1,
            'addition' => $this->number_line_1_addition,
            'comment' => $this->comment,
            'postal_code' => $this->zip_code,
            'city' => $this->city,
            'phone' => $this->phone,
            'email' => $this->email,
            'country' => $this->country,
            'reference' => $this->reference,
            'weight' => $this->weight,
            'amount' => $this->amount,
        ];

        $data = array_merge($data, $this->custom_data);

        return $data;
    }

    public function toLegacyArray(): array
    {
        $data = [
            'product' => $this->product,
            'service' => $this->service,
            'amount' => $this->amount,
            'reference' => $this->reference,
            'company_name' => $this->company_name,
            'contact_person' => $this->contact_person,
            'street_line_1' => $this->street_line_1,
            'number_line_1' => $this->number_line_1,
            'number_line_1_addition' => $this->number_line_1_addition,
            'zip_code' => $this->zip_code,
            'city' => $this->city,
            'country' => $this->country,
            'phone' => $this->phone,
            'email' => $this->email,
            'comment' => $this->comment,
            'weight' => $this->weight,
        ];

        $custom_data = $this->custom_data;
        $options = Arr::pull($custom_data, 'options');
        if ($options) {
            $custom_data = array_merge($custom_data, $options);
        }
        $data = array_merge($data, $custom_data);

        return $data;
    }

    public function createDeliveryableRecord()
    {
        $data = [
            'deliverable_type' => get_class($this->deliverable),
            'deliverable_id' => $this->deliverable->id,
            'carrier' => $this->product,
            'service' => $this->service,
            'amount' => $this->amount,
            'reference' => $this->reference,
            'company_name' => $this->company_name,
            'contact_person' => $this->contact_person,
            'street' => $this->street_line_1,
            'number' => $this->number_line_1,
            'number_addition' => $this->number_line_1_addition,
            'zip_code' => $this->zip_code,
            'city' => $this->city,
            'country' => $this->country,
            'phone' => $this->phone,
            'email' => $this->email,
            'comment' => $this->comment,
            'weight' => $this->weight,
            'extra_data' => $this->custom_data,
            'payload' => $this->toArray(),
        ];

        if (config('keen-delivery.use_legacy')) {
            $data['payload'] = $this->toLegacyArray();
        }

        return KeenDelivery::$deliveryModel::create($data);
    }

    public function getDeliverable()
    {
        return $this->deliverable;
    }

    public function create()
    {
        if (config('keen-delivery.use_legacy')) {
            return KeenDeliveryApi::createShipment($this);
        }
        return SendyApi::createShipment($this);
    }

    public function of(Model $deliverable)
    {
        $this->deliverable = $deliverable;
        return $this;
    }

    public function product(ParcelCarriers $carrier): self
    {
        $this->product = $carrier->getProduct();
        return $this;
    }

    public function service(string $service): self
    {
        $this->service = $service;
        return $this;
    }

    public function amount(int $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function reference(string $reference = null): self
    {
        $this->reference = $reference;
        return $this;
    }

    public function companyName(string $company_name = null): self
    {
        $this->company_name = Str::limit($company_name, 35, '');
        return $this;
    }

    public function contactPerson(string $contact_person = null): self
    {
        $this->contact_person = $contact_person;
        return $this;
    }

    public function street(string $street_line_1): self
    {
        $this->street_line_1 = $street_line_1;
        return $this;
    }

    public function number(string $number_line_1): self
    {
        $this->number_line_1 = $number_line_1;
        return $this;
    }

    public function numberAddition(string $number_line_1_addition = null): self
    {
        $this->number_line_1_addition = $number_line_1_addition;
        return $this;
    }

    public function zipCode(string $zip_code): self
    {
        $this->zip_code = $zip_code;
        return $this;
    }

    public function city(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function country(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function phone(string $phone = null): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function email(string $email = null): self
    {
        $this->email = $email;
        return $this;
    }

    public function comment(string $comment = null): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function weight(int $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function customData(array $custom_data): self
    {
        $this->custom_data = $custom_data;
        return $this;
    }
}
