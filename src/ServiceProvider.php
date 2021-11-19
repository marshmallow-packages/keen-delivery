<?php

/**
 * ServiceProvider for our Kiyoh Package
 *
 * PHP version 7.4
 *
 * @category Reviews
 * @package  Kiyoh
 * @author   Stef van Esch <stef@marshmallow.dev>
 * @license  MIT Licence
 * @link     https://marshmallow.dev
 */

namespace Marshmallow\KeenDelivery;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * ServiceProvider for our Kiyoh Package
 *
 * @category Reviews
 * @package  Kiyoh
 * @author   Stef van Esch <stef@marshmallow.dev>
 * @license  MIT Licence
 * @link     https://marshmallow.dev
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/keen-delivery.php',
            'keen-delivery'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../config/keen-delivery.php' => config_path('keen-delivery.php'),
        ]);
    }
}
