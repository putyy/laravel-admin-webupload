<?php
declare(strict_types=1);

namespace Pt\LaravelAdminWebUpload\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Pt\LaravelAdminWebUpload\Params\AliyunParam;
use Pt\LaravelAdminWebUpload\Params\LocalParam;
use Pt\LaravelAdminWebUpload\Params\QiniuParam;

abstract class WebUploadController extends Controller
{
    public function scene(Request $request): JsonResponse
    {
        try {
            $platform = $request->get('platform');
            $scene = $request->get('scene');
            $other = $request->get('other');
            switch ($platform) {
                case 'qiniu':
                    $data = $this->qiniu($scene, $other);
                    break;
                case 'aliyun':
                    $data = $this->aliyun($scene, $other);
                    break;
                case 'local':
                default:
                    $data = $this->local($scene, $other);
            }
            return response()->json([
                'code' => 1,
                'msg' => 'ok',
                'data' => $data
            ]);
        } catch (\Throwable $throwable) {
            return response()->json([
                'code' => 0,
                'msg' => $throwable->getMessage(),
                'data' => null
            ]);
        }
    }

    /**
     * 自定义存储前端直传上传参数
     * @param string $scene
     * @param string $other
     * @return LocalParam
     */
    abstract protected function local(string $scene, string $other): LocalParam;

    /**
     * 七牛云前端直传上传参数
     * @param string $scene
     * @param string $other
     * @return QiniuParam
     */
    abstract protected function qiniu(string $scene, string $other): QiniuParam;

    /**
     * 阿里云前端直传上传参数
     * @param string $scene
     * @param string $other
     * @return AliyunParam
     */
    abstract protected function aliyun(string $scene, string $other): AliyunParam;
}
