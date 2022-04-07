<?php
declare(strict_types=1);

namespace Pt\LaravelAdminWebUpload\Form;

use Encore\Admin\Form\Field;

class VideoKit extends Field
{
    use BaseKit;

    protected $view = 'laravel-admin-webupload::file';

    public function render()
    {
        $this->setDefaultAttribute()->uploadType(3)->defaultAttribute('accept', 'video/*');
        return parent::render();
    }
}
