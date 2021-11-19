<?php

namespace Marshmallow\KeenDelivery\Nova;

use App\Nova\Resource;
use Eminiarts\Tabs\Tabs;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Text;
use Eminiarts\Tabs\TabsOnEdit;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Textarea;

class Delivery extends Resource
{
    use TabsOnEdit;

    public static $model = 'Marshmallow\KeenDelivery\Models\Delivery';

    public static $title = 'reference';

    public static $search = [
        'carrier_shipping_id',
        'track_and_trace_id',
        'track_and_trace_url',
        'carrier',
        'service',
        'reference',
        'company_name',
        'contact_person',
        'street',
        'zip_code',
        'city',
        'phone',
        'email',
        'comment',
    ];

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * Fields for this resource. Extended by the fields
     * that are default by the package
     *
     * @param Request $request Request object
     *
     * @return array Array of fields
     */
    public function fields(Request $request)
    {
        return [
            new Tabs(__('Delivery'), [
                __('Shipment') => [
                    ID::make()->sortable(),
                    MorphTo::make('deliverable')->types(
                        config('keen-delivery.delivery_models')
                    ),
                    Text::make(__('Shipping ID'), 'carrier_shipping_id')->hideFromIndex(),
                    Stack::make(__('Method'), [
                        Line::make(__('Carrier'), 'carrier')->asSubTitle(),
                        Line::make(__('Service'), 'service')->asSmall(),
                    ]),
                    Text::make(__('Track & Trace'), 'track_and_trace_id')->resolveUsing(function ($value, $delivery) {
                        $track_and_trace = $delivery->track_and_trace_url;
                        if ($track_and_trace) {
                            return '<a href="' . $track_and_trace . '" target="_blank">' . $value . '</a>';
                        }

                        return __('n/a');
                    })->asHtml(),
                    Text::make(__('Track & Trace URL'), 'track_and_trace_url')->hideFromIndex(),

                    Text::make(__('Label'), 'label_encoded')->resolveUsing(function ($value, $delivery) {
                        if ($delivery->hasLabel()) {
                            return '<a href="' . $delivery->downloadLabelRoute() . '">' . __('Download') . '</a>';
                        }

                        return __('n/a');
                    })->asHtml(),

                    Text::make(__('Amount'), 'amount')->hideFromIndex(),
                    Text::make(__('Weight'), 'weight')->hideFromIndex(),
                    Text::make(__('Reference'), 'reference')->hideFromIndex(),
                    DateTime::make(__('Created'), 'created_at'),
                ],
                __('Person') => [
                    Text::make(__('Company'), 'company_name')->hideFromIndex(),
                    Text::make(__('Person'), 'contact_person')->hideFromIndex(),
                    Text::make(__('Phone'), 'phone')->hideFromIndex(),
                    Text::make(__('Email'), 'email')->hideFromIndex(),
                ],
                __('Address') => [
                    Text::make(__('Street'), 'street')->hideFromIndex(),
                    Text::make(__('Number'), 'number')->hideFromIndex(),
                    Text::make(__('Number addition'), 'number_addition')->hideFromIndex(),
                    Text::make(__('Zip code'), 'zip_code')->hideFromIndex(),
                    Text::make(__('City'), 'city')->hideFromIndex(),
                    Text::make(__('Country'), 'country')->hideFromIndex(),
                ],
                __('Extra data') => [
                    Code::make(__('Extra data'), 'extra_data')->json(),
                    Textarea::make(__('Comment'), 'comment')->hideFromIndex(),
                ],
                __('Request') => [
                    Code::make(__('Payload'), 'payload')->json(),
                    Code::make(__('Response'), 'response')->json(),
                    Badge::make('Status')->map([
                        __('success') => 'success',
                        __('has errors') => 'danger',
                    ])->resolveUsing(function ($value, $delivery) {
                        return $delivery->getNovaStatus();
                    }),
                ],
            ]),
        ];
    }

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    /**
     * Cards for this resource. Extended by the cards
     * that are default by the package
     *
     * @param Request $request Request object
     *
     * @return array Array of cards
     */
    public function cards(Request $request)
    {
        return [];
    }


    /**
     * Filters for this resource. Extended by the filters
     * that are default by the package
     *
     * @param Request $request Request object
     *
     * @return array Array of filters
     */
    public function filters(Request $request)
    {
        return [];
    }


    /**
     * Lenses for this resource. Extended by the lenses
     * that are default by the package
     *
     * @param Request $request Request object
     *
     * @return array Array of lenses
     */
    public function lenses(Request $request)
    {
        return [];
    }


    /**
     * Actions for this resource. Extended by the actions
     * that are default by the package
     *
     * @param Request $request Request object
     *
     * @return array Array of actions
     */
    public function actions(Request $request)
    {
        return [];
    }
}
