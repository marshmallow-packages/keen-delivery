<?php

namespace Marshmallow\KeenDelivery\Http\Controllers;

use App\Http\Controllers\Controller;
use Marshmallow\KeenDelivery\KeenDelivery;
use Marshmallow\KeenDelivery\Http\Controllers\Traits\FileDownload;

class DownloadLabelController extends Controller
{
    use FileDownload;

    public function __invoke($delivery)
    {
        $delivery = KeenDelivery::$deliveryModel::findOrFail($delivery);
        abort_unless($delivery->hasLabel(), 404);

        $label_file_name = $delivery->getLabelName();

        return $this->download(
            $label_file_name,
            $delivery->getLabelContent()
        );
    }
}
