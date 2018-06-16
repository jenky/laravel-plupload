<?php

namespace Jenky\LaravelPlupload\Contracts;

use Closure;

interface Plupload
{
    /**
     * Create Plupload builder.
     *
     * @param  string $id
     * @param  string $url
     * @return \Jenky\LaravelPlupload\Html
     */
    public function make($id, $url);

    /**
     * Plupload file upload handler.
     *
     * @param  string $name
     * @param  closure $closure
     * @return void
     */
    public function file($name, Closure $closure);
}
