//公用ajax
// var HOSTNAME = 'http://127.0.0.1:3334';
// var HOSTNAME = 'http://dev.admin.crm.9daye.com.cn';
// var HOSTNAME = 'http://192.168.0.177:8081/hummerServer';

var times = 1

function requestUrl(parms) {
    var _default = {
        url: '',
        dataType: 'json',
        type: 'GET',
        data: {
        },
        success: function (data) {

        },
        error: function (err) {
            if (err.responseJSON.status == 403) {
                if (err.responseJSON.data.errMsg == "没有权限.请通知管理员添加权限.") {
                    salert("", err.responseJSON.data.errMsg, "warning", false);
                } else if (err.responseJSON.data.errMsg == "用户未登录") {
                    swal({
                        title: err.responseJSON.data.errMsg,
                        text: "2秒后自动跳转。",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(function () {
                        location.href = './login.html';
                    }, 2000);
                }

            } else {
                salert("", err.responseJSON.data.errMsg, "warning", false);
            }
        }

    }
    $.extend(_default, parms);
    _default.data.token = window.localStorage.getItem('BUSINESS_TOKEN');
    var _host = _default.url;
    _default.url = _host;
    $.ajax(_default)
}

// 获取数据
function getData(option) {
    requestUrl({
        url: option.url,
        data: option.data,
        success: function (data) {
            if (data.errorMsg) {
                if (data.errorMsg.match('Token无效')) {
                    if (times === 1) {
                        times = 2
                        alert(data.errorMsg)
                        location.href = './login.html'
                    }
                } else {
                    alert(data.errorMsg)
                }
            } else {
                if (data.result) {
                    if (data.result.status == 200 || data.result.status == undefined) {
                        option.success(data)
                    } else {
                        alert(data.result.message)
                    }
                } else {
                    console.log('data.result 没有值')
                }
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(textStatus || errorThrown)
        }
    })
}
var salert = function (title, text, type, isCancelButton, callback) {
    swal({
        title: title,
        text: text,
        type: type,
        showCancelButton: isCancelButton,
        confirmButtonClass: "btn-normal",
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnConfirm: true,
        animation: false
    }, callback);

}


// 分页
var pagingBuilder = {
    build: function (dom, page, size, total) {
        var total = Math.ceil(total / size);
        var pagination = '';
        pagination += '<ul class="pagination J_page_box" data-max="' + total + '">';
        if (total < 7) {
            for (var i = 1; i < total + 1; i++) {
                if (i == page) {
                    pagination += '<li class="active" data-page="' + i + '"><a href="javascript:;">' + i + '</a></li>';
                    continue
                }
                pagination += '<li data-page="' + i + '"><a href="javascript:;">' + i + '</a></li>';
            }
        } else {
            if (page < 5) {
                for (var i = 1; i < 6; i++) {
                    if (i == page) {
                        pagination += '<li class="active" data-page="' + i + '"><a href="javascript:;">' + i + '</a></li>';
                        continue
                    }
                    pagination += '<li data-page="' + i + '"><a href="javascript:;">' + i + '</a></li>';
                }
                pagination += '<li><span>...</span></li>';
            } else {
                pagination += '<li data-page="1"><a href="javascript:;">1</a></li>\
                            <li><span>...</span></li>';
                var loop = total - page;
                if (total - page > 3) {
                    loop = 3;
                }
                for (var i = 0; i < (loop + 2); i++) {
                    if (i == 2) {
                        pagination += '<li class="active" data-page="' + (page - 2 + i) + '"><a href="javascript:;">' + (page - 2 + i) + '</a></li>';
                        continue
                    }
                    pagination += '<li data-page="' + (page - 2 + i) + '"><a href="javascript:;">' + (page - 2 + i) + '</a></li>'
                }
                if (total - page > 3) {
                    pagination += '<li><span>...</span></li>';
                }
            }
            if (page == total) {
                pagination += '<li class="active" data-page="' + total + '"><a href="javascript:;">' + total + '</a></li>';
            } else {
                pagination += '<li data-page="' + total + '"><a href="javascript:;">' + total + '</a></li>';
            }
        }
        pagination += '</ul>\
                    <div class="pagination-info J_page_search">\
                        <span>共' + total + '页，到第<input type="text" maxlength="3" onfocus="this.select()" value="1">页</span>\
                        <a href="javascript:;" class="btn btn-default">确认</a>\
                    </div>';
        dom && dom.html(pagination);
        dom.find('.J_page_search input').on('keyup', function () {
            this.value = this.value.replace(/[^1-9]/g, '')

            // var number = $(this).val().replace(/\D/g,'') - 0;
            // $(this).val(number);
            // if ($(this).val().length < 1) {
            //     $(this).val('1');
            //     return false
            // }
            // if ($(this).val() < 1) {
            //     $(this).val('1');
            //     return false
            // }
            // if ($(this).val() > dom.find('.J_page_box').data('max')) {
            //     $(this).val(dom.find('.J_page_box').data('max'))
            //     return false
            // }
        })
    },
    click: function (dom, fn) {
        dom.find('.J_page_box li').off().on('click', function () {
            var val = $(this).data('page');
            if (val == undefined) {
                return false
            }
            if (typeof (fn) == 'function') {
                fn(val)
            }
        })
        dom.find('.J_page_search a').off().on('click', function () {
            var n = dom.find('.J_page_search input').val();
            if (n > dom.find('.J_page_box').data('max')) {
                alert('已超过最大分页数')
                return false;
            }
            if (typeof (fn) == 'function') {
                fn(n)
            }
        })
    }
}

//上传图片

function uploadImg(fileInfo, opt) {
    getPermission(fileInfo, function (permissionData) {
        var formData = new FormData()
        var xhr = new XMLHttpRequest()

        for (var i in permissionData) {
            formData.append(i, permissionData[i])
        }
        formData.append('file', fileInfo.imgFile)

        xhr.open('POST', permissionData.host)

        xhr.onload = function () {
            if (xhr.status === 200) {
                typeof opt.loaded === 'function' && opt.loaded(JSON.parse(xhr.responseText).data)
            } else {
                typeof opt.error === 'function' && opt.error(xhr)
            }
        }

        xhr.upload.onprogress = function (event) {
            if (event.lengthComputable) {
                var complete = (event.loaded / event.total * 100 | 0)
                typeof opt.loading === 'function' && opt.loading(complete)
            }
        }

        xhr.send(formData)
    })
}

function getPermission(fileInfo, callback) {
    var xhr = new XMLHttpRequest()

    xhr.onreadystatechange = handler
    xhr.open('GET', '/interceptor/uploadImg?' + [
        'userType=' + fileInfo.userType,
        'token=' + localStorage.getItem('BUSINESS_TOKEN'),
        'imgType=' + fileInfo.imgType,
        'fileName=' + fileInfo.imgFile.name,
        'objectType=' + fileInfo.objectType,
        'technicianId=' + fileInfo.technicianId
    ].join('&'))
    xhr.send(null)

    function handler() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200 || xhr.status === 304) {
                var JSONFormatedData = JSON.parse(xhr.responseText)
                callback(JSONFormatedData.result.data)
            }
        }
    }
}


// 全局执行
$(function () {
    var url = location.pathname;
    var target = $('.business-frame-aside').find('a[href="' + url + '"]');
    target.removeClass('collapsed');
    target.parents('li').addClass('active').siblings().removeClass('active');
    target.parents('.collapse').addClass('in').siblings('a').removeClass('collapsed');
})


    // juicer 辅助函数
    ; (function () {

        // 订单管理状态映射
        juicer.register('order_map_status', function (data) {
            return ['待付款', '已付款', '已派单', '交易完成', '交易关闭', '已取消', '已过期'][data - 1] || ''
        });

        juicer.register('order_pay_type', function (data) {
            return ['支付宝', '微信'][data - 1] || ''
        })

        //订单管理服务状态映射
        juicer.register('orderservice_map_status', function (data) {
            if (data == -1) {
                return '已过期'
            }
            else {
                return ['待支付', '待分配', '待施工', '施工中', '施工完成', '服务完成', '已取消', '分配失败'][data] || ''
            }
        });

        //非空验证
        juicer.register('isNull', function (data) {
            if (data == null) {
                return ''
            } else {
                return data
            }
        })

    }());

// 导航栏
; (function () {
    // 页面初始化
    function init() {

        // 用户名
        $('span[data-id="username"]').html(localStorage.getItem('BUSINESS_USERNAME'))

        // 登出时清空 localStorage 存储的 BUSINESS_TOKEN，然后转到 login.html
        $('a[data-id="logout"]').on('click', function () {
            localStorage.removeItem('BUSINESS_TOKEN')
            localStorage.removeItem('BUSINESS_USERNAME')
            requestUrl({
                url: '/authentication/account/logout.do',
                data: 'GET',
                success: function (res) {
                    console.log(res);
                    location.href = './login.html'
                },
                error: function (err) {
                    console.log(err)
                }
            })
        })
    }

    init()
}());


