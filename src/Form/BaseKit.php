<?php
declare(strict_types=1);

namespace Pt\LaravelAdminWebUpload\Form;

trait BaseKit
{
    /**
     * @param string $attribute
     * @param string $value
     *
     * @return $this
     */
    protected function defaultAttribute($attribute, $value): static
    {
        if (!array_key_exists($attribute, $this->attributes)) {
            $this->attribute($attribute, $value);
        }

        return $this;
    }

    protected function setDefaultAttribute(): static
    {
        // 30 * 1024 * 1024 == 31457280
        return $this->defaultAttribute('data-max', 31457280)
            ->defaultAttribute('data-scene', '')
            ->defaultAttribute('data-other', '')
            ->defaultAttribute('data-platform', 'local');
    }

    public function toQiniu(): static
    {
        return $this->defaultAttribute('data-platform', 'qiniu');
    }

    public function toAli(): static
    {
        return $this->defaultAttribute('data-platform', 'aliyun');
    }

    public function toLocal(): static
    {
        return $this->defaultAttribute('data-platform', 'local');
    }
}
