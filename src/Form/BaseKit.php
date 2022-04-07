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
        return $this->defaultAttribute('data-size', 31457280)
            ->defaultAttribute('data-scene', '')
            ->defaultAttribute('data-other', '')
            ->defaultAttribute('data-platform', 'local');
    }

    public function toQiniu(): static
    {
        return $this->defaultAttribute('data-platform', 'qiniu');
    }

    public function toAliyun(): static
    {
        return $this->defaultAttribute('data-platform', 'aliyun');
    }

    public function toLocal(): static
    {
        return $this->defaultAttribute('data-platform', 'local');
    }

    protected function uploadType(int $type = 1): static
    {
        // upload-type 1图片 2音频 3视频 4文件
        return $this->addVariables(['uploadType' => $type]);
    }

    public function kitFiles(array $data): static
    {
        $this->view = 'laravel-admin-webupload::files';
        $this->addVariables(array_merge([
            'src' => 'src',
            // 最大数量
            'max'=>12,
            // 最少数量 -1不限制
            'min'=>1,
// 文件列表
//            'files' => [
//                [
//                    'other'=>'',
//                    'src'=>'',
//                ]
//            ]
        ],$data));
        return $this;
    }
}
