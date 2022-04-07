var rewriteSubmitApp = {
    data: {
        scene_url: null,
        isResQiniuJs: false,
        isLoading: false,
        files: [],
    },
    main: function (scene_url) {
        this.data.scene_url = scene_url;
        if ($('.kit-rewrite-submit').length === 0) {
            $(".btn-primary[type='submit']").after('<button type="button" class="btn btn-primary kit-rewrite-submit">提交</button>').hide();
        }
        this.attachEvent();
    },
    attachEvent: function () {
        $('.kit-file').on('click', '.glyphicon', function (e) {
            if ($(this).hasClass('glyphicon-chevron-left')) {
                if ($(this).parents('.kit-item').prev().length) {
                    $(this).parents('.kit-item').prev().before($(this).parents('.kit-item').remove());
                }
                return;
            }

            if ($(this).hasClass('glyphicon-chevron-right')) {
                if ($(this).parents('.kit-item').next().length) {
                    $(this).parents('.kit-item').next().after($(this).parents('.kit-item').remove());
                }
                return;
            }

            if ($(this).hasClass('glyphicon-remove')) {
                $(this).parents('.kit-item').remove();
                return;
            }
        });

        $(".select-file").on("change", function (e) {
            var fileObj = $(this)[0].files[0];
            if (typeof (fileObj) == "undefined" || fileObj.size <= 0) {
                toastr.error("请选文件", "", {timeOut: 3});
                return false;
            }
            var size = $(this).attr("data-size");
            if (fileObj.size > size) {
                toastr.error("文件最大不能超过" + (size / 1024 / 1024) + "M", "", {timeOut: 3});
                return false;
            }
            var type = parseInt($(this).attr('data-type'));
            var kitFileDiv = $(this).parent().find(".kit-file");
            switch (type) {
                case 1:
                    kitFileDiv.find('img').attr('src', URL.createObjectURL(fileObj));
                    break;
                case 2:
                    kitFileDiv.find('audio>source').attr('src', URL.createObjectURL(fileObj));
                    kitFileDiv.find('audio').load();
                    break;
                case 3:
                    kitFileDiv.find('video>source').attr('src', URL.createObjectURL(fileObj));
                    kitFileDiv.find('video').load();
                    break;
            }
        });
        $(".select-files").on("change", function (e) {
            var kitFileDiv = $(this).parent().find(".kit-file");
            var type = parseInt($(this).attr('data-type'));
            var num = parseInt($(this).attr('data-num'));
            var size = $(this).attr("data-size");
            var itemNum = kitFileDiv.find('.kit-item').length;
            var fileObj = $(this)[0].files
            if (itemNum >= num) {
                toastr.error("最多只能添加" + num + "个", "", {timeOut: 3});
                $(this).val(null);
                return false;
            }
            if (fileObj.length > (num - itemNum)) {
                toastr.error("最多还能选择" + (num - itemNum) + "个", "", {timeOut: 3});
                $(this).val(null);
                return false;
            }
            var msg = '';
            for (var i = 0; i < fileObj.length; i++) {
                if (fileObj[i].size > size) {
                    msg += fileObj[i].name + ','
                    continue;
                }
                kitFileDiv.append(
                    rewriteSubmitApp.method.itemHtml({
                        type: type,
                        index: rewriteSubmitApp.data.files.push(fileObj[i]),
                        src: URL.createObjectURL(fileObj[i]),
                    })
                )
            }

            if (msg) {
                toastr.error("文件最大不能超过" + (size / 1024 / 1024) + "M," + msg + '文件超过限制已丢弃，请重新选择', "", {timeOut: 3});
                return false;
            }
        });

        $(".kit-rewrite-submit").click(function () {

            $('.kit-rewrite-submit').val("处理中...");
            if (rewriteSubmitApp.data.isLoading) {
                return;
            }
            rewriteSubmitApp.data.isLoading = true;

            if ($('.select-files').length > 0) {
                var isBreak = false;
                $('.select-files').each(function (e, that) {
                    var data = $(this).data();
                    if (isBreak===false && data.min != -1 && $(that).parents('.form-group').find('.kit-item').length <= 0) {
                        // 限制必须
                        toastr.error('"' + $(that).parents('.form-group').find('label').text() + '"必须', "", {timeOut: 3});
                        isBreak = true;
                        return;
                    }
                });
                if(isBreak){
                    return;
                }
            }

            var pro = new Promise(function (resolve, reject) {
                resolve();
            });


            // 多文件上传
            $('.select-files').each(function (e, that1) {
                var itemData = [];
                var uploadType = parseInt($(that1).parents('.form-group').find('.select-files').attr('data-type'));
                $(that1).parents('.form-group').find(".kit-file .kit-item").each(function (e, that) {
                    var data = $(that).data();
                    if (!data.index) {
                        switch (uploadType) {
                            case 1:
                                itemData.push($(that).find('img').attr('src') + "#" + data.other)
                                break;
                            case 2:
                            case 3:
                                itemData.push($(that).find('source').attr('src') + "#" + data.other)
                                break;
                            case 4:
                                itemData.push($(that).find('.file-name').text() + "#" + data.other)
                                break;
                        }

                        return;
                    }
                    data.index = data.index - 1;
                    pro = pro.then(function () {
                        return new Promise(function (resolve, reject) {
                            var config = $(that).parents('.form-group').find('.select-files').data()
                            var res = rewriteSubmitApp.method.scene({
                                other: config.other,
                                type: config.type,
                                platform: config.platform,
                                scene: config.scene
                            })

                            if (res.code === 0) {
                                rewriteSubmitApp.data.isLoading = false;
                                reject({msg: res.msg});
                                return;
                            }

                            var sceneData = res.data;
                            var file = rewriteSubmitApp.data.files[data.index]
                            switch (config.platform) {
                                case "qiniu":
                                    rewriteSubmitApp.method.qiniu({
                                        file: file,
                                        key: sceneData.key,
                                        token: sceneData.token,
                                    }, function (res) {
                                        if (res.code === 0) {
                                            rewriteSubmitApp.data.isLoading = false;
                                            toastr.error(res.res.message, "", {timeOut: 3});
                                            reject({msg: res.res.message});
                                            return;
                                        }
                                        // todo success
                                        $(that).parent().find(".kit-data").val(res.key)
                                        $(that).val(null);
                                        itemData.push(res.key + "#" + data.other)
                                        resolve();
                                    });
                                    break;
                                case "aliyun":
                                    rewriteSubmitApp.method.aliyun({
                                        file: file,
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
                                            toastr.error("上传到阿里云失败", "", {timeOut: 3});
                                            reject({msg: "上传到阿里云失败"});
                                            return;
                                        }
                                        // todo success
                                        $(that).parent().find(".kit-data").val(res.key)
                                        $(that).val(null);
                                        itemData.push(res.key + "#" + data.other)
                                        resolve();
                                    });
                                    break;
                                case 'local':
                                default:
                                    rewriteSubmitApp.method.local({
                                        file: file,
                                        server_url: sceneData.server_url,
                                        key: sceneData.key,
                                        url: sceneData.url,
                                        token: sceneData.token,
                                    }, function (res) {
                                        if (res.code === 0) {
                                            rewriteSubmitApp.data.isLoading = false;
                                            toastr.error("上传到本地失败", "", {timeOut: 3});
                                            reject({msg: "上传到本地失败"});
                                            return;
                                        }
                                        // todo success
                                        $(that).parent().find(".kit-data").val(res.key)
                                        $(that).val(null);
                                        itemData.push(res.key + "#" + data.other)
                                        resolve();
                                    });
                            }
                        })
                    });
                });
                pro = pro.then(function () {
                    return new Promise(function (resolve, reject) {
                        if (itemData.length > 0) {
                            $(that1).parents('.form-group').find('.kit-data').val(itemData.join('&'))
                        }
                        console.log("多文件完")
                        resolve();
                    });
                });
            });

            // 单文件上传
            $(".select-file").each(function (e, that) {
                if (!$(that)[0].files[0]) {
                    return;
                }
                pro = pro.then(function () {
                    return new Promise(function (resolve, reject) {
                        var data = $(that).data();

                        var res = rewriteSubmitApp.method.scene({
                            other: data.other,
                            type: data.type,
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
                                        toastr.error(res.res.message, "", {timeOut: 3});
                                        reject({msg: res.res.message});
                                        return;
                                    }
                                    $(that).parent().find(".kit-data").val(res.key)
                                    $(that).val(null);
                                    // todo success
                                    resolve();
                                });
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
                                        toastr.error("上传到阿里云失败", "", {timeOut: 3});
                                        reject({msg: "上传到阿里云失败"});
                                        return;
                                    }
                                    // todo success
                                    $(that).parent().find(".kit-data").val(res.key)
                                    $(that).val(null);
                                    resolve();
                                });
                                break;
                            case 'local':
                            default:
                                rewriteSubmitApp.method.local({
                                    file: $(that)[0].files[0],
                                    server_url: sceneData.server_url,
                                    key: sceneData.key,
                                    url: sceneData.url,
                                    token: sceneData.token,
                                }, function (res) {
                                    if (res.code === 0) {
                                        rewriteSubmitApp.data.isLoading = false;
                                        toastr.error("上传到本地失败", "", {timeOut: 3});
                                        reject({msg: "上传到本地失败"});
                                        return;
                                    }
                                    // todo success
                                    $(that).parent().find(".kit-data").val(res.key)
                                    $(that).val(null);
                                    resolve();
                                });
                        }
                    })
                });
            });

            pro.then(function () {
                return new Promise(function (resolve, reject) {
                    // console.log("提交")
                    // return;
                    $('.kit-rewrite-submit').val("提交");
                    rewriteSubmitApp.data.isLoading = false;
                    $(".btn-primary[type='submit']").click();
                });
            })
        });
    },
    method: {
        itemHtml: function (data) {
            var str = '<div class="kit-item" data-other="" data-index="' + data.index + '">\n' +
                '                    <div class="option-icon">\n' +
                '                        <span class="glyphicon glyphicon-chevron-left"></span>\n' +
                '                        <span class="glyphicon glyphicon-chevron-right"></span>\n' +
                '                        <span class="glyphicon glyphicon-remove"></span>\n' +
                '                    </div>';
            switch (data.type) {
                case 1:
                    str += ' <img src="' + data.src + '"/>';
                    break;
                case 2:
                    str += '<audio controls><source src="' + data.src + '"/> </audio><p>' + data.src + '</p>';
                    break;
                case 3:
                    str += '<video controls><source src="' + data.src + '"/></video><p>' + data.src + '</p>';
                    break;
                case 4:
                    str += '<div class="file-name">' + data.src + '</div>';
                    break;
            }

            return str + '  </div>';
        },
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
                processData: false,
                cache: false,
                async: false,
                contentType: false,
                type: 'post',
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
