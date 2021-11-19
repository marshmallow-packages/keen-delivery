<?php

namespace Marshmallow\KeenDelivery\Nova\Actions;

use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\InteractsWithQueue;
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
        $download_path = KeenDelivery::getBulkDownloadPath($models);

        return Action::download($download_path, 'shipment-labels.pdf');
    }
}
