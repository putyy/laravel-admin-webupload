<?php
declare(strict_types=1);

namespace Pt\LaravelAdminWebUpload\Form;

use Encore\Admin\Form\Field;

class ImageKit extends Field
{
    use BaseKit;

    protected $view = 'laravel-admin-webupload::image';

    public function render()
    {
        $this->setDefaultAttribute()->defaultAttribute('accept', 'image/*');
        return parent::render();
    }
}
