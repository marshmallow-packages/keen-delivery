![alt text](https://marshmallow.dev/cdn/media/logo-red-237x46.png "marshmallow.")

# Laravel Keen Delivery

[![Latest Version on Packagist](https://img.shields.io/packagist/v/marshmallow/keen-delivery.svg?v=1)](https://github.com/marshmallow-packages/keen-delivery)
[![Total Downloads](https://img.shields.io/packagist/dt/marshmallow/keen-delivery.svg?v=1)](https://github.com/marshmallow-packages/keen-delivery)
[![Issues](https://img.shields.io/github/issues/marshmallow-packages/keen-delivery)](https://github.com/marshmallow-packages/keen-delivery)
[![Stars](https://img.shields.io/github/stars/marshmallow-packages/keen-delivery)](https://github.com/marshmallow-packages/keen-delivery)
[![Forks](https://img.shields.io/github/forks/marshmallow-packages/keen-delivery)](https://github.com/marshmallow-packages/keen-delivery)

This package makes it easy to link with the Keen Delivery API. You connect it by simply adding a trait to your model which should be able to be shipped. The package takes care of the rest. We've also added a number of features to make it easy to manage your shipments via Laravel Nova.

![DeliveryActions](https://marshmallow.dev/cdn/readme/keen-delivery/DeliveryActions.png)

## Installation

You can install this package using composer.

```bash
composer require marshmallow/keen-delivery
```

### Migrations

Run the migrations. The migration will create a table in your database where all shipments will be stored. We will also store the request and response to/of the Keen Delivery API.

```bash
php artisan migrate
```

### Publish the config

After publishing the config, please change the config to your specifications. Below you will find an explanation of all the config values.

```bash
php artisan vendor:publish --provider="Marshmallow\KeenDelivery\ServiceProvider" --tag="config"
```

| Key                     | #1                                                                                                                                                                                                                                    |
| ----------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| api_path                | This is the base path of the Keen Delivery API.                                                                                                                                                                                       |
| api_token               | This will load your API key from you `.env` file                                                                                                                                                                                      |
| default_carrier         | This will hold the default carrier you wish to use. You can also provide this manually when creating a shipment. Please reference the Carriers table at the bottom of this README file to see which carries are currently supported.  |
| default_carrier_service | This will hold the default service you wish to use. You can also provide this manually when creating a shipment. Please reference the Carriers table at the bottom of this README file to see which services are currently supported. |
| delivery_models         | Add any Nova Resource that should be able to be shipped to this array. By default we will add `App\Nova\Order` because this is most common in our own projects                                                                        |
| routes                  | This holds the routes which are used for download shipment labels. These are added to the config to make this package highly customizable.                                                                                            |
| routes.\*.path          | The path of the route that is loaded.                                                                                                                                                                                                 |
| routes.\*.name          | The name of the route                                                                                                                                                                                                                 |
| routes.\*.controller    | The controller connected to the route. Please not that this controller should have an `__invoke()` method where all the magic happens.                                                                                                |
| routes.\*.ttl           | The routes that are generated for downloading a label or downloading bulk labels are signed routes. Here you can add the amount of minutes that this signed URL should be valid.                                                      |

### Update your .env

Add your Keen Delivery API token to your `.env` file. You can also add this to your config but this is not advised.

```env
KEEN_DELIVERY_API_TOKEN="*****"
```

## Usage

### Add the trait to your model

First you need to add the `KeenDelivery` trait to the model which can be shipped.

```php
use Marshmallow\KeenDelivery\Traits\KeenDelivery;

class Order
{
    use KeenDelivery;
    // ...
```

### Implement abstract methods

Next you need to implement all the methods we need to create a shipment with KeenDelivery. See the example below.

```php
use Marshmallow\KeenDelivery\Traits\KeenDelivery;

class Order
{
    use KeenDelivery;

    // ...

    /**
     * Keen Delivery
     */
    public function getDeliveryReference(): string
    {
        return __('Shipment for order: #:order_id', [
            'order_id' => $this->id,
        ]);
    }

    public function getDeliveryCompanyName(): ?string
    {
        return $this->shippingAddress()->company_name;
    }

    public function getDeliveryContactPerson(): ?string
    {
        return $this->shippingAddress()->name;
    }

    public function getDeliveryStreet(): string
    {
        return $this->shippingAddress()->address;
    }

    public function getDeliveryNumber(): string
    {
        return $this->shippingAddress()->house_number;
    }

    public function getDeliveryAddition(): ?string
    {
        return $this->shippingAddress()->house_number_addon;
    }

    public function getDeliveryZipCode(): string
    {
        return $this->shippingAddress()->postal_code;
    }

    public function getDeliveryCity(): string
    {
        return $this->shippingAddress()->city;
    }

    public function getDeliveryCountry(): string
    {
        return $this->shippingAddress()->country?->id ?? 'NL';
    }

    public function getDeliveryPhone(): ?string
    {
        return $this->customer->phone_number;
    }

    public function getDeliveryEmail(): ?string
    {
        return $this->customer->email;
    }

    public function getDeliveryComment(): ?string
    {
        return null;
    }

    public function getDeliveryPredict(): ?string
    {
        return '';
    }

    /**
     * Return the weight in kilo's
     */
    public function getDeliveryWeight(): ?int
    {
        return 1;
    }

    public function getCustomDeliveryData(): array
    {
        return [
            /**
             * Use email notifications for DPD
             */
            'predict' => 2,
        ];
    }

```

### Create a shipment

After you have done this, you can create your shipping labels like so:

```php
$order->createShipment()
```

### Events

This package will trigger event on some actions. Below you will find an overview of the events that you can hook in to.

| Event                                            | When                                                        | Variable    |
| ------------------------------------------------ | ----------------------------------------------------------- | ----------- |
| \Marshmallow\KeenDelivery\Events\ShipmentCreated | This will be triggered everytime a new shipment is created. | `$delivery` |

## Use in Laravel Nova

If you want to use this package in Nova, you must start by publishing the Nova Resource. This can be done by running the command below. This command will create the file `app\Nova\Delivery.php` if it does not already exist.

![DeliveryDetail](https://marshmallow.dev/cdn/readme/keen-delivery/DeliveryDetail.png)

```bash
php artisan marshmallow:resource Delivery KeenDelivery
```

## Update your own resource

In this example, we assume that you want to be able to link your orders to this package. We have a number of useful functions that give you a lot of information and possibilities in Nova. In this example we are talking about orders, however, this can be applied to all your models and resources.

### Show the shipments

Add the relationship below to `App\Nova\Order` to make the shipments visible in the detail view of your orders.

![DeliveryIndex](https://marshmallow.dev/cdn/readme/keen-delivery/DeliveryIndex.png)

```php
public function fields(Request $request)
{
    // ...
    MorphMany::make(__('Deliveries'), 'deliverable', Delivery::class),
}
```

### Show Tracking information on your index

We have created a helper that allows you to add the `Track & Trace` number and a download button to download the shipping label to the index of your resource.

![OrderIndex](https://marshmallow.dev/cdn/readme/keen-delivery/OrderIndex.png)

```php
use Marshmallow\KeenDelivery\Facades\KeenDelivery;

public function fields(Request $request)
{
    // ...
    KeenDelivery::shipmentInfoField(),
}
```

### Filters

We also have a filter ready by default. With this filter, you can filter on all orders that have been successfully submitted to Keen Delivery. You can also filter on orders that have not yet or not successfully been submitted with Keen Delivery.

![DeliveryFilters](https://marshmallow.dev/cdn/readme/keen-delivery/DeliveryFilters.png)

```php
public function filters(Request $request)
{
    return [
        new ShipmentNotifiedFilter,
    ];
}
```

### Actions

There are two actions in this package that will make your life extra easy.

![DeliveryActions](https://marshmallow.dev/cdn/readme/keen-delivery/DeliveryActions.png)

#### Submit for shipmet

With this action you can submit an order from Nova to Keen Delivery. It is also possible to submit multiple orders at the same time.

```php
public function actions(Request $request)
{
    return [
        //
        new SubmitForShipment,
    ];
}
```

#### Download labels

With this action you can download the label of a shipment. It is also possible to download the shipping labels of multiple orders at once.

```php
public function actions(Request $request)
{
    return [
        //
        new DownloadLabels,
    ];
}
```

## API

The following API methods are useful for the setup of this package. With the `verifyApiToken` you can check if you actually have a working connection with the Keen Deliver API and with `listShippingMethods` you can see which shipping methods are available to you.

```php
use Marshmallow\KeenDelivery\Facades\KeenDeliveryApi;

KeenDeliveryApi::verifyApiToken();
KeenDeliveryApi::listShippingMethods();
```

## Carriers

The tables below show which carries and which services are currently supported by this package.

| DHL                                  |                                                                          |
| ------------------------------------ | ------------------------------------------------------------------------ |
| DHL_FOR_YOU_MAILBOX_PACKAGE_PICK_UP  | DHL For You brievenbuspakket (naar particulieren) - Pick-up              |
| DHL_FOR_YOU_MAILBOX_PACKAGE_DROP_OFF | DHL For You brievenbuspakket (naar particulieren) - Inleveren parcelshop |
| DHL_EUROPLUS_PALLET                  | Europlus Pallet (naar bedrijven)                                         |
| DHL_EUROPLUS_PALLET_INTERNATIONAL    | Europlus Pallet Internationaal (naar bedrijven)                          |
| DHL_EUROPLUS_PALLET_RETURN           | Retour - Europlus Pallet (bij bedrijf op laten halen)                    |
| DHL_FOR_YOU_DROP_OFF_ADDRESS         | DHL For You (naar particulieren) - Inleveren parcelshop                  |
| DHL_EUROPLUS_DROP_OFF                | Europlus (naar bedrijven) - Inleveren parcelshop                         |
| DHL_PARCEL_CONNECT                   | Parcel Connect (naar particulieren en bedrijven)                         |
| DHL_PARCEL_CONNECT_DROP_OFF          | Parcel Connect (naar particulieren en bedrijven)                         |
| DHL_FOR_YOU_DROP_OFF_RETURN          | Retour DHL For You (klant levert in bij Parcelshop)                      |
| DHL_FOR_YOU_PICK_UP_ADDRESS          | DHL For You (naar particulieren) - Pick-up                               |
| DHL_EUROPLUS_PICK_UP                 | Europlus (naar bedrijven) - Pick-up                                      |
| DHL_EUROPLUS_INTERNATIONAL_DROP_OFF  | Europlus Internationaal (naar particulieren en bedrijven)                |
| DHL_EUROPLUS_INTERNATIONAL_PICK_UP   | Europlus Internationaal (naar particulieren en bedrijven)                |
| DHL_FOR_YOU_PICK_UP_PARCELSHOP       | DHL For You 2Shop (naar Parcelshop)                                      |
| DHL_FOR_YOU_DROP_OFF_PARCELSHOP      | DHL For You 2Shop (naar Parcelshop)                                      |
| DHL_EXPRESSER                        | Expresser (voor 11.00 uur leveren bij bedrijven)                         |
| DHL_FOR_YOU_PICK_UP_RETURN           | Retour DHL For You (bij particulier op laten halen)                      |
| DHL_EUROPLUS_RETURN                  | Retour Europlus (bij bedrijf op laten halen)                             |

| DPD                   |                                         |
| --------------------- | --------------------------------------- |
| DPD_HOME_DROP_OFF     | Home (naar particulieren) - Inleveren   |
| DPD_HOME_PICK_UP      | Home (naar particulieren) - Pick-up     |
| DPD_CLASSIC_DROP_OFF  | Classic (naar bedrijven) - Inleveren    |
| DPD_CLASSIC_PICK_UP   | Classic (naar bedrijven) - Pick-up      |
| DPD_TO_SHOP_DROP_OFF  | 2Shop (naar parcelshop) - Inleveren     |
| DPD_TO_SHOP_PICK_UP   | 2Shop (naar parcelshop) - Pick-up       |
| DPD_10_PICK_UP        | DPD 10:00 (voor 10:00 leveren)          |
| DPD_12_PICK_UP        | DPD 12:00 (voor 12:00 leveren)          |
| DPD_18_PICK_UP        | DPD 18:00 (voor 18:00 leveren)          |
| DPD_GUARANTEE_PICK_UP | DPD Guarantee 18:00                     |
| DPD_RETURN_DROP_OFF   | Retour (klant levert in bij parcelshop) |
| DPD_RETURN_PICK_UP    | Retour (bij klant op laten halen)       |

| PostNL                     |                                                              |
| -------------------------- | ------------------------------------------------------------ |
| POSTNL_MP                  | Brievenbuspakje+                                             |
| DOMESTIC_PACKAGE           | Pakketten NL (naar particulieren & bedrijven)                |
| DOMESTIC_PACKAGE_M         | Pakketten NL (naar particulieren & bedrijven) - M            |
| DOMESTIC_PACKAGE_L         | Pakketten NL (naar particulieren & bedrijven) - L            |
| DOMESTIC_PACKAGE_XL        | Pakketten NL (naar particulieren & bedrijven) - XL           |
| TO_SHOP                    | PakjeGemak (naar PostNL locatie)                             |
| TO_SHOP_M                  | PakjeGemak (naar PostNL locatie) - M                         |
| TO_SHOP_L                  | PakjeGemak (naar PostNL locatie) - L                         |
| TO_SHOP_XL                 | PakjeGemak (naar PostNL locatie) - XL                        |
| GLOBAL_PACK                | Pakketten Non-EU (naar particulieren & bedrijven)            |
| EPS_PACKAGE_CONSUMER       | Pakketten EU (naar particulieren)                            |
| EPS_PACKAGE_BUSINESS       | Pakketten EU (naar bedrijven)                                |
| DOMESTIC_PACKAGE_RETURN    | Retourpakketten NL (klant levert in bij PostNL locatie)      |
| DOMESTIC_PACKAGE_RETURN_M  | Retourpakketten NL (klant levert in bij PostNL locatie) - M  |
| DOMESTIC_PACKAGE_RETURN_L  | Retourpakketten NL (klant levert in bij PostNL locatie) - L  |
| DOMESTIC_PACKAGE_RETURN_XL | Retourpakketten NL (klant levert in bij PostNL locatie) - Xl |

| TNT |                 |
| --- | --------------- |
| 48N | Economy Express |

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email stef@marshmallow.dev instead of using the issue tracker.

## Credits

-   [Stef van Esch](https://github.com/stefvanesch)
-   [Marshmallow](https://github.com/marshmallow-packages)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Resources

API documentation: https://keendelivery.readthedocs.io/en/latest/

---

Copyright (c) 2021 marshmallow.
