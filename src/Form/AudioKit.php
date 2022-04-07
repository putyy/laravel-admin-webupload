<?php
declare(strict_types=1);

namespace Pt\LaravelAdminWebUpload\Form;

use Encore\Admin\Form\Field;

class AudioKit extends Field
{
    use BaseKit;

    protected $view = 'laravel-admin-webupload::file';

    public function render()
    {
        $this->setDefaultAttribute()->uploadType(2)->defaultAttribute('accept', 'audio/mp3');
        return parent::render();
    }
}
