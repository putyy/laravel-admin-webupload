<?php
declare(strict_types=1);

namespace Pt\LaravelAdminWebUpload;

use Encore\Admin\Admin;
use Encore\Admin\Form;
use Illuminate\Support\ServiceProvider;
use Pt\LaravelAdminWebUpload\Form\AudioKit;
use Pt\LaravelAdminWebUpload\Form\FileKit;
use Pt\LaravelAdminWebUpload\Form\ImageKit;
use Pt\LaravelAdminWebUpload\Form\RewriteSubmitKit;
use Pt\LaravelAdminWebUpload\Form\VideoKit;

class LaravelAdminWebUploadServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(LaravelAdminWebUpload $extension)
    {
        if (! LaravelAdminWebUpload::boot()) {
            return ;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'laravel-admin-webupload');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/putyy/laravel-admin-webupload')],
                'laravel-admin-webupload'
            );
        }
        Admin::booting(function () {
            Form::extend('imageKit', ImageKit::class);
            Form::extend('audioKit', AudioKit::class);
            Form::extend('videoKit', VideoKit::class);
            Form::extend('fileKit', FileKit::class);
            Form::extend('rewriteSubmitKit', RewriteSubmitKit::class);
        });
    }
}
