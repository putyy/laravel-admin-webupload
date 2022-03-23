<?php
declare(strict_types=1);

namespace Pt\LaravelAdminWebUpload;

class AliyunParam extends \stdClass
{
    /**
     * 文件存储路径
     * @var
     */
    public $key;

    /**
     * 上传后访问地址
     * @var
     */
    public $url;

    /**
     * @var
     */
    public $accessid;

    /**
     * 上传地址
     * @var
     */
    public $server_url;

    /**
     * @var
     */
    public $policy;

    /**
     * @var
     */
    public $signature;

    /**
     * @var
     */
    public $expire;

}
