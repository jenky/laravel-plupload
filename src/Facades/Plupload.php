<?php

namespace Jenky\LaravelPlupload\Facades;

use Illuminate\Support\Facades\Facade;
use Jenky\LaravelPlupload\Contracts\Plupload as PluploadContract;

class Plupload extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PluploadContract::class;
    }
}
