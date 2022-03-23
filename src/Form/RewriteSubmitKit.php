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
            'form_url' => $this->variables['form_url'] ?? '',
            'scene_url' => $this->variables['scene_url'] ?? '',
        ]);
        return parent::render();
    }
}
