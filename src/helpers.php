<?php

use Jenky\LaravelPlupload\Contracts\Plupload;

if (! function_exists('plupload')) {
    /**
     * @param  null|string $id
     * @param  null|string $url
     * @return \Jenky\LaravelPlupload\Contracts\Plupload|\Jenky\LaravelPlupload\Html
     */
    function plupload($id = null, $url = null)
    {
        $factory = app(Plupload::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($id, $url);
    }
}
