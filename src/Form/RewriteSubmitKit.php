<?php
declare(strict_types=1);

namespace Pt\LaravelAdminWebUpload\Form;

use Encore\Admin\Form\Field;

class RewriteSubmitKit extends Field
{
    protected $view = 'laravel-admin-webupload::rewrite-submit';

    public function render()
    {
        $this->addVariables([
            'scene_url' => $this->variables['scene_url'] ?? '/admin/upload-scene',
        ]);
        return parent::render();
    }
}
