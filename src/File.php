<?php

namespace Jenky\LaravelPlupload;

use Closure;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class File
{
    /**
     * @var Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $storage;

    /**
     * @var int
     */
    private $maxFileAge = 600; // 600 seconds

    /**
     * Create new class instance.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Filesystem\Filesystem $file
     * @return void
     */
    public function __construct(Request $request, Filesystem $file)
    {
        $this->request = $request;
        $this->storage = $file;
    }

    /**
     * Get chuck upload path.
     *
     * @return string
     */
    public function getChunkPath()
    {
        $path = config('plupload.chunk_path');

        if (! $this->storage->isDirectory($path)) {
            $this->storage->makeDirectory($path, 0777, true);
        }

        return $path;
    }

    /**
     * Process uploaded files.
     *
     * @param  string $name
     * @param  \Closure $closure
     * @return array
     */
    public function process($name, Closure $closure)
    {
        $response = [];
        $response['jsonrpc'] = '2.0';

        if ($this->hasChunks()) {
            $result = $this->chunks($name, $closure);
        } else {
            $result = $this->single($name, $closure);
        }

        $response['result'] = $result;

        return $response;
    }

    /**
     * Handle single uploaded file.
     *
     * @param  string $name
     * @param  \Closure $closure
     * @return void
     */
    public function single($name, Closure $closure)
    {
        if ($this->request->hasFile($name)) {
            return $closure($this->request->file($name));
        }
    }

    /**
     * Handle single uploaded file.
     *
     * @param  string $name
     * @param  \Closure $closure
     * @return mixed
     */
    public function chunks($name, Closure $closure)
    {
        if (! $this->request->hasFile($name)) {
            return;
        }

        $file = $this->request->file($name);
        $chunk = (int) $this->request->input('chunk', false);
        $chunks = (int) $this->request->input('chunks', false);
        $originalName = $this->request->input('name');

        $filePath = $this->getChunkPath().'/'.$originalName.'.part';

        $this->removeOldData($filePath);
        $this->appendData($filePath, $file);

        if ($chunk == $chunks - 1) {
            $file = new UploadedFile($filePath, $originalName, 'blob', UPLOAD_ERR_OK, true);
            @unlink($filePath);

            return $closure($file);
        }
    }

    /**
     * Remove old chunks.
     *
     * @param  string $filePath
     * @return void
     */
    protected function removeOldData($filePath)
    {
        if ($this->storage->exists($filePath) && ($this->storage->lastModified($filePath) < time() - $this->maxFileAge)) {
            $this->storage->delete($filePath);
        }
    }

    /**
     * Merge chunks.
     *
     * @param  string $filePathPartial
     * @param  \Illuminate\Http\UploadedFile $file
     * @return void
     */
    protected function appendData($filePathPartial, UploadedFile $file)
    {
        if (! $out = @fopen($filePathPartial, 'ab')) {
            throw new Exception('Failed to open output stream.', 102);
        }

        if (! $in = @fopen($file->getPathname(), 'rb')) {
            throw new Exception('Failed to open input stream', 101);
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);
    }

    /**
     * Check if request has chunks.
     *
     * @return bool
     */
    public function hasChunks()
    {
        return (bool) $this->request->input('chunks', false);
    }
}
