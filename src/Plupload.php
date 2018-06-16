<?php

namespace Jenky\LaravelPlupload;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Jenky\LaravelPlupload\Contracts\Plupload as Contract;

class Plupload implements Contract
{
    /**
     * @var Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Class constructor.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * File upload handler.
     *
     * @param  string $name
     * @param  closure $closure
     * @return void
     */
    public function file($name, Closure $closure)
    {
        $fileHandler = $this->app->make(File::class);

        return $fileHandler->process($name, $closure);
    }

    /**
     * Html template handler.
     *
     * @param  string $id
     * @param  string $url
     * @return \Jenky\LaravelPlupload\Html
     */
    public function make($id, $url)
    {
        return $this->app->make(Html::class, compact('id', 'url'));
    }
}
