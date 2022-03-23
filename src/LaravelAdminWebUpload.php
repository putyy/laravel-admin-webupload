<?php
declare(strict_types=1);

namespace Pt\LaravelAdminWebUpload;

use Encore\Admin\Extension;

class LaravelAdminWebUpload extends Extension
{
    public $name = 'laravel-admin-webupload';

    public $views = __DIR__ . '/../resources/views';

    public $assets = __DIR__ . '/../resources/assets';
}
