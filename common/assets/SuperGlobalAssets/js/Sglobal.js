/**
 * url parser
 * https://github.com/websanova/js-url
 */
window.url = (function() {

    function _t() {
        return new RegExp(/(.*?)\.?([^\.]*?)\.?(com|net|org|biz|ws|in|me|co\.uk|co|org\.uk|ltd\.uk|plc\.uk|me\.uk|edu|mil|br\.com|cn\.com|eu\.com|hu\.com|no\.com|qc\.com|sa\.com|se\.com|se\.net|us\.com|uy\.com|ac|co\.ac|gv\.ac|or\.ac|ac\.ac|af|am|as|at|ac\.at|co\.at|gv\.at|or\.at|asn\.au|com\.au|edu\.au|org\.au|net\.au|id\.au|be|ac\.be|adm\.br|adv\.br|am\.br|arq\.br|art\.br|bio\.br|cng\.br|cnt\.br|com\.br|ecn\.br|eng\.br|esp\.br|etc\.br|eti\.br|fm\.br|fot\.br|fst\.br|g12\.br|gov\.br|ind\.br|inf\.br|jor\.br|lel\.br|med\.br|mil\.br|net\.br|nom\.br|ntr\.br|odo\.br|org\.br|ppg\.br|pro\.br|psc\.br|psi\.br|rec\.br|slg\.br|tmp\.br|tur\.br|tv\.br|vet\.br|zlg\.br|br|ab\.ca|bc\.ca|mb\.ca|nb\.ca|nf\.ca|ns\.ca|nt\.ca|on\.ca|pe\.ca|qc\.ca|sk\.ca|yk\.ca|ca|cc|ac\.cn|com\.cn|edu\.cn|gov\.cn|org\.cn|bj\.cn|sh\.cn|tj\.cn|cq\.cn|he\.cn|nm\.cn|ln\.cn|jl\.cn|hl\.cn|js\.cn|zj\.cn|ah\.cn|gd\.cn|gx\.cn|hi\.cn|sc\.cn|gz\.cn|yn\.cn|xz\.cn|sn\.cn|gs\.cn|qh\.cn|nx\.cn|xj\.cn|tw\.cn|hk\.cn|mo\.cn|cn|cx|cz|de|dk|fo|com\.ec|tm\.fr|com\.fr|asso\.fr|presse\.fr|fr|gf|gs|co\.il|net\.il|ac\.il|k12\.il|gov\.il|muni\.il|ac\.in|co\.in|org\.in|ernet\.in|gov\.in|net\.in|res\.in|is|it|ac\.jp|co\.jp|go\.jp|or\.jp|ne\.jp|ac\.kr|co\.kr|go\.kr|ne\.kr|nm\.kr|or\.kr|li|lt|lu|asso\.mc|tm\.mc|com\.mm|org\.mm|net\.mm|edu\.mm|gov\.mm|ms|nl|no|nu|pl|ro|org\.ro|store\.ro|tm\.ro|firm\.ro|www\.ro|arts\.ro|rec\.ro|info\.ro|nom\.ro|nt\.ro|se|si|com\.sg|org\.sg|net\.sg|gov\.sg|sk|st|tf|ac\.th|co\.th|go\.th|mi\.th|net\.th|or\.th|tm|to|com\.tr|edu\.tr|gov\.tr|k12\.tr|net\.tr|org\.tr|com\.tw|org\.tw|net\.tw|ac\.uk|uk\.com|uk\.net|gb\.com|gb\.net|vg|sh|kz|ch|info|ua|gov|name|pro|ie|hk|com\.hk|org\.hk|net\.hk|edu\.hk|us|tk|cd|by|ad|lv|eu\.lv|bz|es|jp|cl|ag|mobi|eu|co\.nz|org\.nz|net\.nz|maori\.nz|iwi\.nz|io|la|md|sc|sg|vc|tw|travel|my|se|tv|pt|com\.pt|edu\.pt|asia|fi|com\.ve|net\.ve|fi|org\.ve|web\.ve|info\.ve|co\.ve|tel|im|gr|ru|net\.ru|org\.ru|hr|com\.hr|ly|xyz)$/);
    }

    function _d(s) {
      return decodeURIComponent(s.replace(/\+/g, ' '));
    }

    function _i(arg, str) {
        var sptr = arg.charAt(0),
            split = str.split(sptr);

        if (sptr === arg) { return split; }

        arg = parseInt(arg.substring(1), 10);

        return split[arg < 0 ? split.length + arg : arg - 1];
    }

    function _f(arg, str) {
        var sptr = arg.charAt(0),
            split = str.split('&'),
            field = [],
            params = {},
            tmp = [],
            arg2 = arg.substring(1);

        for (var i = 0, ii = split.length; i < ii; i++) {
            field = split[i].match(/(.*?)=(.*)/);

            // TODO: regex should be able to handle this.
            if ( ! field) {
                field = [split[i], split[i], ''];
            }

            if (field[1].replace(/\s/g, '') !== '') {
                field[2] = _d(field[2] || '');

                // If we have a match just return it right away.
                if (arg2 === field[1]) { return field[2]; }

                // Check for array pattern.
                tmp = field[1].match(/(.*)\[([0-9]+)\]/);

                if (tmp) {
                    params[tmp[1]] = params[tmp[1]] || [];
                
                    params[tmp[1]][tmp[2]] = field[2];
                }
                else {
                    params[field[1]] = field[2];
                }
            }
        }

        if (sptr === arg) { return params; }

        return params[arg2];
    }

    return function(arg, url) {
        var _l = {}, tmp, tmp2;

        if (arg === 'tld?') { return _t(); }

        url = url || window.location.toString();

        if ( ! arg) { return url; }

        arg = arg.toString();

        if (tmp = url.match(/^mailto:([^\/].+)/)) {
            _l.protocol = 'mailto';
            _l.email = tmp[1];
        }
        else {

            // Ignore Hashbangs.
            if (tmp = url.match(/(.*?)\/#\!(.*)/)) {
                url = tmp[1] + tmp[2];
            }

            // Hash.
            if (tmp = url.match(/(.*?)#(.*)/)) {
                _l.hash = tmp[2];
                url = tmp[1];
            }

            // Return hash parts.
            if (_l.hash && arg.match(/^#/)) { return _f(arg, _l.hash); }

            // Query
            if (tmp = url.match(/(.*?)\?(.*)/)) {
                _l.query = tmp[2];
                url = tmp[1];
            }

            // Return query parts.
            if (_l.query && arg.match(/^\?/)) { return _f(arg, _l.query); }

            // Protocol.
            if (tmp = url.match(/(.*?)\:?\/\/(.*)/)) {
                _l.protocol = tmp[1].toLowerCase();
                url = tmp[2];
            }

            // Path.
            if (tmp = url.match(/(.*?)(\/.*)/)) {
                _l.path = tmp[2];
                url = tmp[1];
            }

            // Clean up path.
            _l.path = (_l.path || '').replace(/^([^\/])/, '/$1').replace(/\/$/, '');

            // Return path parts.
            if (arg.match(/^[\-0-9]+$/)) { arg = arg.replace(/^([^\/])/, '/$1'); }
            if (arg.match(/^\//)) { return _i(arg, _l.path.substring(1)); }

            // File.
            tmp = _i('/-1', _l.path.substring(1));
            
            if (tmp && (tmp = tmp.match(/(.*?)\.(.*)/))) {
                _l.file = tmp[0];
                _l.filename = tmp[1];
                _l.fileext = tmp[2];
            }

            // Port.
            if (tmp = url.match(/(.*)\:([0-9]+)$/)) {
                _l.port = tmp[2];
                url = tmp[1];
            }

            // Auth.
            if (tmp = url.match(/(.*?)@(.*)/)) {
                _l.auth = tmp[1];
                url = tmp[2];
            }

            // User and pass.
            if (_l.auth) {
                tmp = _l.auth.match(/(.*)\:(.*)/);

                _l.user = tmp ? tmp[1] : _l.auth;
                _l.pass = tmp ? tmp[2] : undefined;
            }

            // Hostname.
            _l.hostname = url.toLowerCase();

            // Return hostname parts.
            if (arg.charAt(0) === '.') { return _i(arg, _l.hostname); }

            // Domain, tld and sub domain.
            if (_t()) {
                tmp = _l.hostname.match(_t());

                if (tmp) {
                    _l.tld = tmp[3];
                    _l.domain = tmp[2] ? tmp[2] + '.' + tmp[3] : undefined;
                    _l.sub = tmp[1] || undefined;
                }
            }

            // Set port and protocol defaults if not set.
            _l.port = _l.port || (_l.protocol === 'https' ? '443' : '80');
            _l.protocol = _l.protocol || (_l.port === '443' ? 'https' : 'http');
        }

        // Return arg.
        if (arg in _l) { return _l[arg]; }

        // Return everything.
        if (arg === '{}') { return _l; }

        // Default to undefined for no match.
        return undefined;
    };
})();


//生成分页
function getPagination(page, total) {
    var pagination = '';
    pagination += '<ul class="pagination" id="J_page_box" data-max="' + total + '">';
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
                pagination += '<li data-page="'+ i +'"><a href="javascript:;">' + i + '</a></li>';
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
            if (total - page >3 ) {
                pagination += '<li><span>...</span></li>';
            }
        }
        if (page == total) {
            pagination += '<li class="active" data-page="' + total + '"><a href="javascript:;">'+ total +'</a></li>';
        } else {
            pagination += '<li data-page="' + total + '"><a href="javascript:;">'+ total +'</a></li>';
        }
    }
    pagination += '</ul>\
                <div class="pagination-info" id="J_page_search">\
                    <span>共' + total + '页，到第<input type="text" maxlength="3" onfocus="this.select()" value="1">页</span>\
                    <a href="javascript:;" class="btn btn-default">确认</a>\
                </div>';
    return pagination;
}
//新分页
var pagingBuilder = {
    build: function(dom, page, size, total){
        var total = Math.ceil(total/size);
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
                    pagination += '<li data-page="'+ i +'"><a href="javascript:;">' + i + '</a></li>';
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
                if (total - page >3 ) {
                    pagination += '<li><span>...</span></li>';
                }
            }
            if (page == total) {
                pagination += '<li class="active" data-page="' + total + '"><a href="javascript:;">'+ total +'</a></li>';
            } else {
                pagination += '<li data-page="' + total + '"><a href="javascript:;">'+ total +'</a></li>';
            }
        }
        pagination += '</ul>\
                    <div class="pagination-info J_page_search">\
                        <span>共' + total + '页，到第<input type="text" maxlength="3" onfocus="this.select()" value="1">页</span>\
                        <a href="javascript:;" class="btn btn-default">确认</a>\
                    </div>';
        dom && dom.html(pagination);
        dom.find('.J_page_search input').on('keyup', function() {
            var number = $(this).val().replace(/\D/g,'') - 0;
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
    click: function(dom, fn) {
        dom.find('.J_page_box li').off().on('click', function() {
            var val = $(this).data('page');
            if (val == undefined) {
                return false
            }
            if (typeof(fn) == 'function') {
                fn(val)   
            }
        })
        dom.find('.J_page_search a').off().on('click', function() {
            var n = dom.find('.J_page_search input').val();
            if (n > dom.find('.J_page_box').data('max')) {
                alert('已超过最大分页数')
                return false;
            }
            if (typeof(fn) == 'function') {
                fn(n)
            }
        })
    },
    clickPage: function(dom, fn) {
        dom.find('.J_page_box li').off().on('click', function() {
            var val = $(this).data('page');
            if (val == undefined) {
                return false
            }
            if (typeof(fn) == 'function') {
                fn(val)   
            }
        })
    },
    clickSearch: function(dom, fn) {
        dom.find('.J_page_search a').off().on('click', function() {
            var n = dom.find('.J_page_search input').val();
            if (n > dom.find('.J_page_box').data('max')) {
                alert('已超过最大分页数')
                return false;
            }
            if (typeof(fn) == 'function') {
                fn(n)
            }
        })
    }
} 

//公用ajax
function requestUrl(url, type, data, callback, error, async) {
    var async = (async == null || async.toString() == "" || typeof(async) == "undefined") ? true : !!async;
    $.ajax({
        url: url,
        method: type,
        async: async,
        data: data
    })
    .done(function( _data ) {
        if (_data.status == 200) {
            callback(_data.data);
        } else {
            if (typeof(error) == 'function') {
                error(_data)
            } else {
                alert(_data.data.errMsg)
            }
        }
    })
    .fail(function( jqXHR, textStatus ) {
        setTimeout(function() {
            requestUrl(url, type, data, callback, error, async);
        }, 10000);
    })
}

//公用验证器
var strategies = {
    isNonEmpty: function(value, errorMsg) {
        if (value === '') {
            return errorMsg;
        }
    },
    minLength: function(value, length, errorMsg) {
        if (value.length < length) {
            return errorMsg;
        }
    },
    maxLength: function(value, length, errorMsg) {
        if (value.length > length) {
            return errorMsg;
        }
    },
    isMobile: function(value, errorMsg) {
        if (!/0?(1)[0-9]{10}/.test(value)) {
            return errorMsg;
        }
    },
    isEmail: function(value, errorMsg) {
        if (!/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/.test(value)) {
            return errorMsg;
        }
    },
    isPassword: function(value, errorMsg) {
        if (/[^0-9a-zA-Z]/g.test(value)) {
            return errorMsg;
        }
    }
};
var Validator = function() {
    this.cache = [];
};
Validator.prototype.add = function(dom, rules) {
    var self = this;
    for (var i = 0, rule; rule = rules[i++];) {
        (function(rule) {
            var strategyAry = rule.strategy.split(':');
            var errorMsg = rule.errorMsg;
            self.cache.push(function() {
                var strategy = strategyAry.shift();
                strategyAry.unshift(dom.val());
                strategyAry.push(errorMsg);
                return strategies[strategy].apply(dom, strategyAry);
            });
        })(rule)
    }
};
Validator.prototype.start = function() {
    for (var i = 0, validatorFunc; validatorFunc = this.cache[i++];) {
        var errorMsg = validatorFunc();
        if (errorMsg) {
            return errorMsg;
        }
    }
};

//oss图片上传
var ossUpload = {
    //获取上传文件后缀
    getSuffix: function(filename) {
        var pos = filename.lastIndexOf('.');
        var suffix = '';
        if (pos != -1) {
            suffix = filename.substring(pos + 1)
        }
        return suffix;
    },
    //配置上传参数
    setUpParam: function($target ,data) {
        var formData = new FormData();
        $.each(data, function(i, n) {
            formData.append(i, n)
        })
        formData.append('file', $target[0].files[0]);
        return formData;
    },
    //上传图片
    uploadImg: function(url, formData, callback) {
        $.ajax({
            url: url.host,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false
        })
        .done(function(data) {
            callback(data);
        })
        .fail(function() {
            alert('上传失败！')
        })
    }
}