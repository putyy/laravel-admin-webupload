<?php
declare(strict_types=1);

namespace Pt\LaravelAdminWebUpload\Form;

use Encore\Admin\Form\Field;

class FileKit extends Field
{
    use BaseKit;

    protected $view = 'laravel-admin-webupload::file';

    public function render()
    {
        $this->setDefaultAttribute()->uploadType(4);
        return parent::render();
    }
}
