<?php

namespace Marshmallow\KeenDelivery\Nova\Actions;

use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\Boolean;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Nova\Http\Requests\NovaRequest;
use Marshmallow\KeenDelivery\Facades\KeenDelivery;

class DownloadLabels extends Action
{
    use InteractsWithQueue, Queueable;

    public function name()
    {
        return __('Download Labels');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $legacy = $fields->legacy ?? false;

        ray($legacy, $fields);
        $download_path = KeenDelivery::getBulkDownloadPath($models, $legacy);

        return Action::download($download_path, 'shipment-labels.pdf');
    }

    public function fields(NovaRequest $request)
    {
        return [
            Boolean::make(__('Download legacy labels'), 'legacy')->default(false),
        ];
    }
}
