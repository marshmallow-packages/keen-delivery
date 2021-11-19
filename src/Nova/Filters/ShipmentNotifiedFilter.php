<?php

namespace Marshmallow\KeenDelivery\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class ShipmentNotifiedFilter extends Filter
{
    public function name()
    {
        return __('Shipment Notified Filter');
    }

    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return match ($value) {
            'submitted' => $query->shipmentSubmitted(),
            'not_submitted' => $query->shipmentNotSubmitted(),
            default => $query,
        };
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            __('Submitted') => 'submitted',
            __('Not submitted') => 'not_submitted',
        ];
    }
}
