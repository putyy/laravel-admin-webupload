var rewriteSubmitApp = {
    data: {
        form_url: null,
        scene_url: null,
        isResQiniuJs: false,
        isLoading: false,
    },
    main: function (form_url, scene_url) {
        this.data.form_url = form_url;
        this.data.form_url = scene_url;
        if (!this.data.form_url) {
            this.data.form_url = $('button:submit').parents('form').attr('action');
        }
        if (!this.data.scene_url) {
            this.data.scene_url = "/admin/upload-scene";
        }
        if ($('.kit-rewrite-submit').length === 0) {
            $(".btn-primary[type='submit']").after('<button type="button" class="btn btn-primary kit-rewrite-submit">提交</button>').hide();
        }
        this.attachEvent();
    },
    attachEvent: function () {
        $(".kit .kit-file").on("change", function (e) {
            var sourceObj = $(this).parent(".kit").find(".source-address");
            if (sourceObj.length > 0) {
                var fileObj = $(this)[0].files[0]
                if (typeof (fileObj) == "undefined" || fileObj.size <= 0) {
                    toastr.error("请选文件", "", {timeOut: 0});
                    return false;
                }

                var max = $(this).attr("data-max");

                if (fileObj.size > max) {
                    toastr.error("文件最大不能超过" + (max / 1024 / 1024) + "M", "", {timeOut: 0});
                    return false;
                }
                sourceObj.attr("src", URL.createObjectURL(fileObj));
                var p = sourceObj.parent();
                ('load' in p[0]) && p[0].load();
            }
        });

        $(".kit-rewrite-submit").click(function () {
            $('.kit-rewrite-submit').val("处理中...");
            if (rewriteSubmitApp.data.isLoading) {
                return;
            }
            rewriteSubmitApp.data.isLoading = true;

            var pro = new Promise(function (resolve, reject) {
                resolve();
            });

            $(".kit-file").each(function (e, that) {
                if (!$(that)[0].files[0]) {
                    return;
                }
                pro = pro.then(function () {
                    return new Promise(function (resolve, reject) {
                        var data = $(that).data();

                        var res = rewriteSubmitApp.method.scene({
                            other: data.other,
                            platform: data.platform,
                            scene: data.scene
                        })

                        if (res.code === 0) {
                            rewriteSubmitApp.data.isLoading = false;
                            reject({msg: res.msg});
                            return;
                        }
                        var sceneData = res.data;
                        switch (data.platform) {
                            case "qiniu":
                                rewriteSubmitApp.method.qiniu({
                                    file: $(that)[0].files[0],
                                    key: sceneData.key,
                                    token: sceneData.token,
                                }, function (res) {
                                    if (res.code === 0) {
                                        rewriteSubmitApp.data.isLoading = false;
                                        toastr.error(res.res.message, "", {timeOut: 0});
                                        reject({msg: res.res.message});
                                        return;
                                    }
                                    $(that).parent().find(".kit-data").val(res.key)
                                    // todo success
                                    resolve();
                                })
                                break;
                            case "aliyun":
                                rewriteSubmitApp.method.aliyun({
                                    file: $(that)[0].files[0],
                                    accessid: sceneData.accessid,
                                    server_url: sceneData.server_url,
                                    policy: sceneData.policy,
                                    signature: sceneData.signature,
                                    expire: sceneData.expire,
                                    key: sceneData.key,
                                    url: sceneData.url,
                                }, function (res) {
                                    if (res.code === 0) {
                                        rewriteSubmitApp.data.isLoading = false;
                                        toastr.error("上传到阿里云失败", "", {timeOut: 0});
                                        reject({msg: "上传到阿里云失败"});
                                        return;
                                    }
                                    // todo success
                                    $(that).parent().find(".kit-data").val(res.key)
                                    resolve();
                                })
                                break;
                            case 'local':
                            default:
                                rewriteSubmitApp.method.aliyun({
                                    file: $(that)[0].files[0],
                                    server_url: sceneData.server_url,
                                    key: sceneData.key,
                                    url: sceneData.url,
                                }, function (res) {
                                    if (res.code === 0) {
                                        rewriteSubmitApp.data.isLoading = false;
                                        toastr.error("上传到本地失败", "", {timeOut: 0});
                                        reject({msg: "上传到本地失败"});
                                        return;
                                    }
                                    // todo success
                                    $(that).parent().find(".kit-data").val(res.key)
                                    resolve();
                                })
                        }
                    })
                });
            })
            pro.then(function () {
                return new Promise(function (resolve, reject) {
                    $('.kit-rewrite-submit').val("提交");
                    rewriteSubmitApp.data.isLoading = false;
                    $(".btn-primary[type='submit']").click();
                });
            })
        });
    },
    method: {
        scene: function (data) {
            var resp;
            $.ajaxSettings.async = false;
            $.get(rewriteSubmitApp.data.scene_url, data, function (res) {
                resp = res
            }, 'json')
            $.ajaxSettings.async = true;
            return resp;
        },
        local: function (data, func) {
            var request = new FormData();
            request.append('key', data.key);
            request.append("token", data.token);
            request.append('file', data.file);//需要上传的文件 file
            $.ajax({
                method: 'post',
                data: request,
                url: data.server_url,
                success: function (callback, res) {
                    func({
                        'code': 1,
                        'key': data.key,
                        'url': data.url,
                    })
                },
                error: function (xhr) {
                    func({
                        'code': 0,
                        'key': data.key,
                        'url': data.url,
                    })
                }
            });
        },
        qiniu: function (data, func) {
            var tempFunc = function () {
                var observer = {
                    next: function next(res) {
                        //进度
                        var str = Number(res.total.percent).toFixed(2) + '%';
                        console.log("qiniu进度:" + str)
                    },
                    error: function error(error) {
                        func({code: 0, key: data.key, url: '', res: error})
                    },
                    complete: function complete(res) {
                        func({code: 1, key: res.key, url: res.url, res: res})
                    }
                };
                // putExtra = {mimeType: ['image/jpg', 'image/jpeg', 'image/png', 'image/gif']};
                var observable = qiniu.upload(data.file, data.key, data.token, null, null);
                var subscription = observable.subscribe(observer); // 上传开始
            };

            if (!rewriteSubmitApp.data.isResQiniu) {
                $.getScript("/assets/js/qiniu.js", function (response, status) {
                    rewriteSubmitApp.data.isResQiniuJs = true;
                    tempFunc();
                });
            } else {
                tempFunc();
            }
        },
        aliyun: function (data, func) {
            var request = new FormData();
            request.append('key', data.key);
            request.append('policy', data.policy);
            request.append('OSSAccessKeyId', data.accessid);
            request.append("Signature", data.signature);
            request.append("success_action_status", '200');// 让服务端返回200,不然，默认会返回204
            request.append('file', data.file);//需要上传的文件 file
            $.ajax({
                url: data.server_url,
                data: request,
                processData: false,
                cache: false,
                async: false,
                contentType: false,
                type: 'post',
                success: function (callback, res) {
                    func({
                        'code': 1,
                        'key': data.key,
                        'url': data.url,
                    })
                },
                error: function (xhr) {
                    func({
                        'code': 0,
                        'key': data.key,
                        'url': data.url,
                    })
                }
            });
        }
    }
};
