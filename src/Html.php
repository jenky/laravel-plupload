<?php

namespace Jenky\LaravelPlupload;

use Illuminate\Contracts\Foundation\Application;

class Html
{
    /**
     * @var Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * @var bool
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $id;

    /**
     * @var bool
     */
    protected $autoStart = false;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $pickFilesButton;

    /**
     * @var string
     */
    protected $uploadButton;

    /**
     * Class constructor.
     *
     * @param string                                       $id
     * @param string                                       $url
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    public function __construct($id, $url, Application $app)
    {
        $this->app = $app;
        $this->initDefaultOptions();

        $this->id = $id;
        $this->options['url'] = $url;
    }

    /**
     * Set default uploader options.
     *
     * @return void
     */
    protected function initDefaultOptions()
    {
        $this->options = array_except($this->app['config']->get('plupload'), ['chunk_path']);
    }

    /**
     * Set default uploader buttons.
     *
     * @param array $options
     *
     * @return void
     */
    protected function initDefaultButtons(array $options)
    {
        if (!$this->pickFilesButton) {
            $this->pickFilesButton = '
				<a class="btn btn-primary btn-browse" id="'.$options['browse_button'].'" href="javascript:;">
					<i class="fa fa-file"></i> '.trans('plupload::ui.browse').'
				</a>
			';
        }

        if (!$this->uploadButton) {
            $this->uploadButton = '
				<a class="btn btn-default btn-upload" id="uploader-'.$this->id.'-upload" href="javascript:;">
					<i class="fa fa-upload"></i> '.trans('plupload::ui.upload').'
				</a>
			';
        }
    }

    /**
     * Initialize the options.
     *
     * @return array
     */
    protected function init()
    {
        if (empty($this->options['url'])) {
            throw new Exception('Missing URL option.');
        }

        $options = [];

        if (empty($this->options['browse_button'])) {
            $options['browse_button'] = 'uploader-'.$this->id.'-pickfiles';
        }

        if (empty($this->options['container'])) {
            $options['container'] = 'uploader-'.$this->id.'-container';
        }

        $options = array_merge($this->options, $options);

        // csrf token
        $options['multipart_params']['_token'] = $this->app['session']->getToken();

        $this->initDefaultButtons($options);

        $id = $this->id;
        $autoStart = $this->autoStart;
        $buttons = [
            'pickFiles' => $this->pickFilesButton,
            'upload'    => $this->uploadButton,
        ];

        $this->data = array_merge($this->data, compact('options', 'id', 'autoStart', 'buttons'));

        return $this->data;
    }

    /**
     * Set uploader auto start.
     *
     * @param bool $bool
     *
     * @return void
     */
    public function setAutoStart($bool)
    {
        $this->autoStart = (bool) $bool;

        return $this;
    }

    /**
     * Set uploader options.
     *
     * @see https://github.com/moxiecode/plupload/wiki/Options
     *
     * @param array $options
     *
     * @return void
     */
    public function setOptions(array $options)
    {
        $options = array_except($options, ['url']);
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Set uploader pick files button.
     *
     * @param string $button
     *
     * @return void
     */
    public function setPickFilesButton($button)
    {
        $this->pickFilesButton = $button;

        return $this;
    }

    /**
     * Set uploader upload button.
     *
     * @param string $button
     *
     * @return void
     */
    public function setUploadButton($button)
    {
        $this->uploadButton = $button;

        return $this;
    }

    /**
     * Set uploader custom params.
     *
     * @param array $params
     *
     * @return void
     */
    public function setCustomParams(array $params)
    {
        $this->data['params'] = $params;

        return $this;
    }

    /**
     * Render the upload handler buttons.
     *
     * @param string $view
     * @param array  $extra
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render($view = 'plupload::uploader', array $extra = [])
    {
        $this->init();

        $this->data = array_merge($this->data, $extra);

        return $this->app['view']->make($view, $this->data);
    }
}
