
# Laravel 5 Plupload

[![Latest Stable Version](https://poser.pugx.org/jenky/laravel-plupload/v/stable.svg)](https://packagist.org/packages/jenky/laravel-plupload)
[![Total Downloads](https://poser.pugx.org/jenky/laravel-plupload/d/total.svg)](https://packagist.org/packages/jenky/laravel-plupload)
[![License](https://poser.pugx.org/jenky/laravel-plupload/license.svg)](https://packagist.org/packages/jenky/laravel-plupload)

##### Laravel package for Plupload http://plupload.com.
This package uses some parts of https://github.com/jildertmiedema/laravel-plupload

## Installation
Require this package with composer:

```
composer require jenky/laravel-plupload
```

**For Laravel 5.4 and below**
After updating composer, add the ServiceProvider to the providers array in `config/app.php`
```php
Jenky\LaravelPlupload\PluploadServiceProvider::class,
```

and add this to your facades in `config/app.php`:

```php
'Plupload' => Jenky\LaravelPlupload\Facades\Plupload::class,
```

Copy the package config to your local config with the publish command:

```
php artisan vendor:publish
```
or
```
php artisan vendor:publish --provider="Jenky\LaravelPlupload\PluploadServiceProvider"
```


## Usage


### Uploading files
##### 1. Use default plupload html

Use the [examples](http://www.plupload.com/examples) found on the plupload site. The [Getting Started](http://plupload.com/docs/Getting-Started) page is good place to start.


##### 2. Plupload builder

**make($id, $url)**

Create new uploader.
* **$id**: the unique identification for the uploader.
* **$url**: the upload url end point.
```php
{!! Plupload::make('my_uploader_id', route('photos.store'))->render() !!}
```
or use the helper
```php
{!! plupload()->make('my_uploader_id', route('photos.store')) !!}
// or event shorter
{!! plupload('my_uploader_id', route('photos.store')) !!}
```

**render($view = 'plupload::uploader', array $data = [])**

Renders the uploader. You can customize this by passing a view name and it's data. From version `2.0`, you can omit the `render` method in the builder if you don't want to set the view or extra data.

##### 3. Use package js file to initialize Plupload (Optional)

If you do not want to write your own js to initialize Plupload, you can use the `upload.js` file that included with the package in `resources/views/vendor/plupload/assets/js`. Make sure that you already have `jQuery` loaded on your page.

**Initialize Plupload**

```js
<script>
$(function () {
    createUploader('my_uploader_id'); // The Id that you used to create with the builder
});
</script>
```


These following methods are useable with the `upload.js` file.

**Set Uploader options**

**setOptions(array $options)**

Set uploader options. Please visit https://github.com/moxiecode/plupload/wiki/Options to see all the options. You can set the default global options in `config/plupload.php`

```php
{!! plupload('my_uploader_id', route('photos.store'))
    ->setOptions([
        'filters' => [
            'max_file_size' => '2mb',
            'mime_types' => [
                ['title' => 'Image files', 'extensions' => 'jpg,gif,png'],
            ],
        ],
    ]) !!}
```

**Automatically start upload when files added**

Use `setAutoStart()` in your builder before calling render() function.

**setAutoStart($bool)**

* **$bool**: `true` or `false`

```php
{!! plupload('my_uploader_id', route('photos.store'))->setAutoStart(true) !!}
```


### Receiving files


**file($name, $handler)**
* **$name**: the input name.
* **$handler**: callback handler.

Use this in your route or your controller. Feel free to modify to suit your needs.

```php
return Plupload::file('file', function($file) {
    // Store the uploaded file using any storage disk
    $path = Storage::disk('local')->putFile('photos', $file);

    // Save the record to the db
    $photo = App\Photo::create([
        'name' => $file->getClientOriginalName(),
        'type' => 'image',
        // ...
    ]);

    // This will be included in JSON response result
    return [
        'success' => true,
        'message' => 'Upload successful.',
        'id' => $photo->id,
        // 'url' => $photo->getImageUrl($filename, 'medium'),
        // 'deleteUrl' => route('photos.destroy', $photo)
        // ...
    ];
});
```
Helper is also available
```php
return plupload()->file('file', function($file) {

});
```


If you are using the package `upload.js` file. The `url` and `deleteUrl` in the JSON payload will be used to generate preview and delete link while the `id` will be appended to the uploader as a hidden field with the following format:

`<input type="hidden" name="{uploaderId}_files[]" value="{id}" />`.

Please note that the `deleteUrl` uses `DELETE` method.
