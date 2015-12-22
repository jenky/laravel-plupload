<?php

namespace Jenky\LaravelPlupload\Facades;

use Illuminate\Support\Facades\Facade;

class Plupload extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'plupload';
    }
}
