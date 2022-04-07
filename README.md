## laravel-admin-webupload
laravel-admin 前端直传封装

### 安装
```shell
composer require putyy/laravel-admin-webupload

php artisan vendor:publish --provider="Pt\LaravelAdminWebUpload\LaravelAdminWebUploadServiceProvider"
``` 

### 使用
#### 这里也可以自行实现 需返回必要的json结果
> 新建控制器如: UploadController, 继承\Pt\LaravelAdminWebUpload\Http\Controllers\WebUploadController 实现对应的方法
> 
> 可以参考 tests/TestUploadController.php 实现
> 
> 添加如下路由
> 
```php
Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {
    // 获取上传场景
    $router->get('upload-scene', [\App\Admin\Controllers\UploadController::class, 'scene']);
    // 自定义文件上传服务
    $router->post('upload-file', [\App\Admin\Controllers\UploadController::class, 'upload']);
});
```

> 表单使用
> 
> $form新增了四个字段类型: imageKit、audioKit、videoKit、fileKit、rewriteSubmitKit
> 
> 除了rewriteSubmitKit其他的可以指定上传属性(data-max、data-scene、data-other)以及上传到指定平台(toQiniu、toAliyun、toLocal)
> >data-max: 限制上传文件的大小
> >
> >data-scene、data-other、data-platform会传到获取上传信息的接口
> 
> rewriteSubmitKit 可指定变量: scene_url(上传所需信息获取地址 默认: /admin/upload-scene)

### 举例
> 单文件:
```php
protected function form()
{
    ...
    // 上传图片到七牛云
    $form->imageKit('img_url', '分类图标')
        ->attribute(['data-scene' => 'shop_date', 'data-other' => 'img'])
        ->toQiniu()
        ->customFormat(function ($value) {
            // 获取图片显示地址
            return $value;
        });
        
    // 上传音频到阿里云
    $form->audioKit('audio_src', '分类图标')
        ->attribute(['data-scene' => 'shop_date', 'data-other' => 'audio'])
        ->toAliyun()
        ->customFormat(function ($value) {
            // 获取音频播放地址
            return $value;
        });
        
    // 上传视频到本地
    $form->videoKit('video_src', '分类图标')
        ->attribute(['data-scene' => 'shop_date', 'data-other' => 'video'])
        ->toLocal()
        ->customFormat(function ($value) {
            // 获取音频播放地址
            return $value;
        });
    
    // 上传文件到本地
    $form->fileKit('table_file', '分类图标')
        ->attribute(['data-scene' => 'shop_date', 'data-other' => 'file'])
        ->toLocal()
        ->customFormat(function ($value) {
            // 获取音频播放地址
            return $value;
        });
    ...
    $form->saving(function (Form $form) {
        // todo 存入数据库时去掉全地址等操作
        // 比如:
        // $form->img_url = "去掉http://xxx.com/ 保留 upload/ss/ss/ss.png";
    });
    // 这一步必须，用于拦截原有提交以便直传文件到对应的服务
    // 变量: scene_url
    $form->rewriteSubmitKit()->addVariables(['scene_url' => '/admin/upload-scene']);
    return $form;
}
```

> 多文件(增加了kitFiles方法):
```php
protected function form()
{
    ...
    // 上传图片到七牛云
    if (request()->isMethod('GET')) {
        $id = request()->route('xxx_model');
        $list = XxxModel::query()->where('goods_id', $id)->get()->toArray();
        $files = [];
        foreach ($list as   $value){
            $files[] = [
                'other'=>$value['id'],
                'src' => getImgUrl($value['img_url']),
            ];
        }
        // 主要在 kitFiles 这方法定义的一些变量 以下两个为必须 其他的可以看 \Pt\LaravelAdminWebUpload\Form\BaseKit::kitFiles
        $form->imageKit('goods_banner', '轮播图')->kitFiles([
            'files' => $files,
        ])
        ->attribute(['data-scene' => SceneConst::GOODS_COVER])
        ->toQiniu();
    }
    ...
    $form->saving(function (Form $form) {
        // todo 存入数据库时去掉全地址等操作
        // 比如:
        // $form->img_url = "去掉http://xxx.com/ 保留 upload/ss/ss/ss.png";
    });
    
     //保存后回调 用于单独处理多文件上传保存
    $form->saved(function (Form $form) {
        //...
        $goods_banner = request()->post('goods_banner');
        $goods_banner = explode('&', $goods_banner);
        $add = [];
        foreach ($goods_banner as $sort => $value) {
            $add[] = [
                'goods_id' => $form->model()->id,
                'img_url' => getThatSavePath(explode('#', $value)[0]),
                'sort' => $sort + 1
            ];
        }
        XxxModel::query()->where('goods_id', $form->model()->id)->delete();
        XxxModel::query()->insert($add);
    });
    
    // 这一步必须，用于拦截原有提交以便直传文件到对应的服务
    // 变量: scene_url
    $form->rewriteSubmitKit();
    return $form;
}
```
### PS: 更多用法请看源码
