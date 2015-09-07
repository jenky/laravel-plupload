<?php


namespace Jenky\LaravelPlupload;

use Closure;
use Illuminate\Http\Request;

class Plupload
{
    /**
     * @var Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
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
        $fileHandler = new File($this->request);

        return $fileHandler->process($name, $closure);
    }

    /**
     * Html template handler.
     * 
     * @param string $id
     * @param string $url
     * 
     * @return void
     */
    public function make($id, $url)
    {
        return new Html($id, $url);
    }
}
