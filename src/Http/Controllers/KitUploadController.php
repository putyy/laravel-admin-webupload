<?php
declare(strict_types=1);

namespace Pt\LaravelAdminWebUpload\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Pt\LaravelAdminWebUpload\AliyunParam;
use Pt\LaravelAdminWebUpload\LocalParam;
use Pt\LaravelAdminWebUpload\QiniuParam;

abstract class KitUploadController extends Controller
{
    public function upload()
    {
        return response()->json([
            'code' => 1,
            'msg' => 'ok',
            'data' => null
        ]);
    }

    public function getScene(Request $request): JsonResponse
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
     * @param string $scene
     * @param string $other
     * @return LocalParam
     */
    abstract protected function local(string $scene, string $other): LocalParam;

    /**
     * @param string $scene
     * @param string $other
     * @return QiniuParam
     */
    abstract protected function qiniu(string $scene, string $other): QiniuParam;

    /**
     * @param string $scene
     * @param string $other
     * @return AliyunParam
     */
    abstract protected function aliyun(string $scene, string $other): AliyunParam;
}
