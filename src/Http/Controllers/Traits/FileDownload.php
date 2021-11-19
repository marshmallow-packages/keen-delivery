<?php

namespace Marshmallow\KeenDelivery\Http\Controllers\Traits;

trait FileDownload
{
    public function __construct()
    {
        $this->middleware('signed');
    }

    public function download(string $label_file_name, string $content)
    {
        $path = public_path($label_file_name);

        /**
         * Temporary store the file to disk.
         */
        file_put_contents(
            $label_file_name,
            $content
        );

        /**
         * Download the file and delete it for security reasons
         */
        return response()->download($path)->deleteFileAfterSend(true);
    }
}
