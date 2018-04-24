//公用ajax
var HOSTNAME = 'http://127.0.0.1:3334';
// var HOSTNAME = 'http://27.115.25.118:8070/humServer';
// var HOSTNAME = 'http://192.168.0.177:8081/hummerServer';
function requestUrl(parms) {
    var _default = {
        url: '',
        type: 'GET',
        data: {},
        success: function (data) {

        },
        error: function (err) {
            alert(err)
        }
    }
    $.extend(_default, parms);
    _default.data.token = window.localStorage.getItem('BUSINESS_TOKEN');
    var _host = _default.url;
    _default.url = HOSTNAME + _host;
    $.ajax(_default)
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
            var number = $(this).val().replace(/\D/g, '') - 0;
            $(this).val(number);
            if ($(this).val().length < 1) {
                $(this).val('1');
                return false
            }
            if ($(this).val() < 1) {
                $(this).val('1');
                return false
            }
            if ($(this).val() > dom.find('.J_page_box').data('max')) {
                $(this).val(dom.find('.J_page_box').data('max'))
                return false
            }
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


// 全局执行
$(function () {
    console.log(test);
    var url = location.pathname;
    var target = $('.business-frame-aside').find('a[href="' + url + '"]');
    target.parents('li').addClass('active');
    target.parents('.collapse').addClass('in').siblings('a').removeClass('collapsed');
})
