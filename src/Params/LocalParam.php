<?php
declare(strict_types=1);

namespace Pt\LaravelAdminWebUpload\Params;

class LocalParam extends \stdClass
{
    /**
     * 文件存储路径
     * @var
     */
    public $key;

    /**
     * 上传token
     * @var
     */
    public $token;

    /**
     * 上传后访问地址
     * @var
     */
    public $url;

    /**
     * 上传地址
     * @var
     */
    public $server_url;
}
