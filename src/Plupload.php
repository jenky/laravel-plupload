<?php

namespace Jenky\LaravelPlupload;

use Closure;
use Illuminate\Contracts\Foundation\Application;

class Plupload
{
    /**
     * @var Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Class constructor.
     *
     * @param Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * File upload handler.
     *
     * @param string  $name
     * @param closure $closure
     *
     * @return void
     */
    public function file($name, Closure $closure)
    {
        $fileHandler = new File($this->app);

        return $fileHandler->process($name, $closure);
    }

    /**
     * Html template handler.
     *
     * @param string $id
     * @param string $url
     *
     * @return \Jenky\LaravelPlupload\Html
     */
    public function make($id, $url)
    {
        return new Html($id, $url, $this->app);
    }
}
