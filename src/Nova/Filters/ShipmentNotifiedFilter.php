<?php

namespace Marshmallow\KeenDelivery\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

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
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(NovaRequest $request, $query, $value)
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
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function options(NovaRequest $request)
    {
        return [
            __('Submitted') => 'submitted',
            __('Not submitted') => 'not_submitted',
        ];
    }
}
