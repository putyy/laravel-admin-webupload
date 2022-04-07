<?php
declare(strict_types=1);

//namespace App\Admin\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Pt\LaravelAdminWebUpload\Http\Controllers\WebUploadController;
use Pt\LaravelAdminWebUpload\Params\AliyunParam;
use Pt\LaravelAdminWebUpload\Params\LocalParam;
use Pt\LaravelAdminWebUpload\Params\QiniuParam;

class TestUploadController extends WebUploadController
{
    /**
     * 本地上传服务
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request)
    {
        try {

            if (!$request->hasFile('file')) {
                throw new \Exception('文件有误1');
            }

            if (!$request->file('file')->isValid()) {
                throw new \Exception('文件有误2');
            }

            // 根据token验证上传文件有效性 token过期、签名不对等效验
//            $token = $request->post('token');
            $key = $request->post('key');
            $file = $request->file('file');
            $fileName = basename($key);
            $dir = public_path() . '/' . str_replace('/' . $fileName, '', $key);
            if (!is_dir($dir)) {
                mkdir($dir, 0750, true);
            }
            $file->move($key, basename($key));
            return response()->json([
                'code' => 1,
                'msg' => 'ok',
                'data' => null
            ]);
        } catch (\Throwable $exception) {
            return response($exception->getMessage(), 500);
        }
    }

    const SCENES = [
        'head_img' => 'head_img',
        'article' => 'article',
        'shop_goods' => 'shop_goods',
    ];

    protected function suffix(int $type): string
    {
        $suffix = '';
        switch ($type) {
            case 1:
                $suffix = '.png';
                break;
            case 2:
                $suffix = '.mp3';
                break;
            case 3:
                $suffix = '.mp4';
                break;
            default:
                break;
        }
        return $suffix;
    }

    protected function createKey(string $scene): string
    {
        return 'upload/' . (self::SCENES[$scene] ?? 'default') . '/' . date('Y/m/d') . '/' . uniqid(date('Ymd'));
    }

    /**
     * @param string $scene
     * @param int $type 1图片 2音频 3视频 4文件
     * @param string $other
     * @return LocalParam
     */
    protected function local(string $scene, int $type, string $other = ''): LocalParam
    {
        // TODO: Implement local() method.
        $config = config('upload.local');
        $param = new LocalParam();
        $param->key = $this->createKey($scene) . $this->suffix($type);
        $param->url = $config['domain'] . '/' . $param->key;;
        // 可以存储到redis 或者 切换可解密的方式生成token, 在server_url 对应的服务对token鉴权
        $param->token = md5($param->key);
        $param->server_url = $config['server_url'];
        return $param;
    }

    /**
     * 需要安装 qiniu/php-sdk 包.
     * @param string $scene
     * @param int $type 1图片 2音频 3视频 4文件
     * @param string $other
     * @return QiniuParam
     */
    protected function qiniu(string $scene, int $type, string $other = ''): QiniuParam
    {
        // TODO: Implement qiniu() method.
        $config = config('upload.qiniu');
        $returnBody = '{"key":"$(key)","hash":"$(etag)","fsize":$(fsize),"bucket":"$(bucket)","name":"$(x:name)","url":"' . $config['domain'] . '/' . '$(key)"}';
        $policy = array('returnBody' => $returnBody);
        $auth = new \Qiniu\Auth($config['ak'], $config['sk']);
        $key = $this->createKey($scene) . $this->suffix($type);
        $param = new QiniuParam();
        $param->url = $config['domain'] . '/' . $key;
        $param->key = $key;
        $param->token = $auth->uploadToken($config['bucket'], $key, 3600, $policy);
        return $param;
    }

    /**
     * @param string $scene
     * @param int $type 1图片 2音频 3视频 4文件
     * @param string $other
     * @return AliyunParam
     * @throws \Exception
     */
    protected function aliyun(string $scene, int $type, string $other = ''): AliyunParam
    {
        // TODO: Implement aliyun() method.
        $config = config('upload.aliyun');
        $key = $this->createKey($scene) . $this->suffix($type);

        $now = time();
        // policy 有效时间s, policy过了这个有效时间，将不能使用
        $expire = 30;
        $end = $now + $expire;

        $dtStr = date("c", $end);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format('Y-m-d\TH:i:sO');
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        $expiration = $expiration . "Z";

        //最大文件大小字节.用户可以自己设置 30*1024*1024
        $condition = [0 => 'content-length-range', 1 => 0, 2 => 31457280];
        $conditions[] = $condition;

        // 表示用户上传的数据，必须是以$dir开始，不然上传会失败，这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
        $start = [0 => 'starts-with', 1 => '$key', 2 => $key];
        $conditions[] = $start;

        $arr = ['expiration' => $expiration, 'conditions' => $conditions];
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;

        // $config['sk'] AccessKeySecret
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $config['sk'], true));

        $param = new AliyunParam();
        $param->accessid = $config['ak'];
        // $host的格式为 bucketname.endpoint
        // $host = 'http://bucket-name.oss-cn-hangzhou.aliyuncs.com';
        $param->server_url = str_replace("http://", "http://" . $config['bucket'] . '.', $config['endpoint']);
        $param->policy = $base64_policy;
        $param->signature = $signature;
        $param->expire = $end;
        $param->key = $key;
        $param->url = $config['domain'] . '/' . $key;
        return $param;
    }
}
