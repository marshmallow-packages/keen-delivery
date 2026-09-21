<?php

namespace Marshmallow\KeenDelivery\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Marshmallow\KeenDelivery\Facades\SendcloudApi;

class SendcloudSetupCommand extends Command
{
    protected $signature = 'keen-delivery:sendcloud-setup {--country=NL : Destination country to list shipping options for}';

    protected $description = 'List the Sendcloud sender addresses and shipping option codes available for the configured account';

    public function handle(): int
    {
        $this->info('Sender addresses (use the id as SENDCLOUD_SENDER_ADDRESS_ID):');

        $this->table(
            ['id', 'company', 'street', 'postal code', 'city', 'country'],
            collect(SendcloudApi::listSenderAddresses())->map(fn (array $address) => [
                $address['id'] ?? null,
                $address['company_name'] ?? null,
                trim(($address['street'] ?? '').' '.($address['house_number'] ?? '')),
                $address['postal_code'] ?? null,
                $address['city'] ?? null,
                $address['country'] ?? null,
            ])->all(),
        );

        $country = strtoupper($this->option('country'));

        $this->info("Shipping options to {$country} (map these codes in keen-delivery.sendcloud.shipping_options):");

        $this->table(
            ['code', 'name', 'contract id', 'max weight'],
            collect(SendcloudApi::listShippingOptions([
                'to_address' => ['country_code' => $country],
            ]))->map(fn (array $option) => [
                $option['code'] ?? null,
                Arr::get($option, 'product.name', $option['name'] ?? null),
                Arr::get($option, 'contract.id'),
                trim(Arr::get($option, 'weight.max.value', '').' '.Arr::get($option, 'weight.max.unit', '')),
            ])->all(),
        );

        $this->comment('Done.');

        return self::SUCCESS;
    }
}
