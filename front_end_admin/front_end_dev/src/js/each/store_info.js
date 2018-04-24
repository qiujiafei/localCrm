/* global $, moment, requestUrl, pagingBuilder, juicer */

$(function () {
    var $starttimeOn = $('input[data-id="starttimeOn"]');
    var $endtimeOn = $('input[data-id="endtimeOn"]');
    var $starttimeStop = $('input[data-id="starttimeStop"]');
    var $endtimeStop = $('input[data-id="endtimeStop"]');
    var $searchOn = $('span[data-id="searchOn"]');
    var $searchStop = $('span[data-id="searchStop"]');

    var _storeTpl = $('#J_tpl_storeOn').html();
    var _storeStopTpl = $('#J_tpl_storeStop').html();
    $endtimeOn.val('')
    //搜索条件暂存

    var str_starttimeOn = ''
    var str_endtimeOn = ''
    var str_typeOn = ''
    var str_storeTpl = ''
    
    var str_starttimeStop = ''
    var str_endtimeStop = ''
    var str_typeStop = ''
    var str_storeStopTpl = ''
    //登录时间

    $.fn.datepicker.dates["zh-CN"] = {
        days: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"],
        daysShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"],
        daysMin: ["日", "一", "二", "三", "四", "五", "六"],
        months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
        monthsShort: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
        today: "今日",
        clear: "清除",
        format: "yyyy年mm月dd日",
        titleFormat: "yyyy年mm月",
        weekStart: 1
        }
        // init the datepicker
        $('.date-picker').datepicker({
        format: 'yyyy-mm-dd',
        language: 'zh-CN',
        orientation: 'bottom'
        });
    // $starttimeOn.daterangepicker({
    //     singleDatePicker: true,
    //     autoApply: true,
    //     locale : {  
    //         format: 'YYYY-MM-DD',
    //         applyLabel : '确定',  
    //         cancelLabel : '取消',  
    //         fromLabel : '起始时间',  
    //         toLabel : '结束时间',  
    //         customRangeLabel : '自定义',  
    //         daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],  
    //         monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月',  
    //                 '七月', '八月', '九月', '十月', '十一月', '十二月' ],  
    //         // firstDay : 1  
    //     },
    //     maxDate: moment().add(-1,'days'),
    //     // startDate: moment().add(-1, 'day')
    // });

    // $endtimeOn.daterangepicker({
    //     singleDatePicker: true,
    //     locale : {  
    //         format: 'YYYY-MM-DD',
    //         applyLabel : '确定',  
    //         cancelLabel : '取消',  
    //         fromLabel : '起始时间',  
    //         toLabel : '结束时间',  
    //         customRangeLabel : '自定义',  
    //         daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],  
    //         monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月',  
    //                 '七月', '八月', '九月', '十月', '十一月', '十二月' ],  
    //         // firstDay : 1  
    //     },  
    //     maxDate: moment()
    // });

    //  //禁用时间
    //  $starttimeStop.daterangepicker({
    //     singleDatePicker: true,
    //     locale : {  
    //         format: 'YYYY-MM-DD',
    //         applyLabel : '确定',  
    //         cancelLabel : '取消',  
    //         fromLabel : '起始时间',  
    //         toLabel : '结束时间',  
    //         customRangeLabel : '自定义',  
    //         daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],  
    //         monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月',  
    //                 '七月', '八月', '九月', '十月', '十一月', '十二月' ],  
    //         firstDay : 1  
    //     },
    //     maxDate: moment().add(-1,'days'),
    //     startDate: moment().add(-1, 'day')
    // });

    // $endtimeStop.daterangepicker({
    //     singleDatePicker: true,
    //     locale : {  
    //         format: 'YYYY-MM-DD',
    //         applyLabel : '确定',  
    //         cancelLabel : '取消',  
    //         fromLabel : '起始时间',  
    //         toLabel : '结束时间',  
    //         customRangeLabel : '自定义',  
    //         daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],  
    //         monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月',  
    //                 '七月', '八月', '九月', '十月', '十一月', '十二月' ],  
    //         firstDay : 1  
    //     },  
    //     maxDate: moment()
    // });

    // function changTime(time) {
    //     var thisTime = new Date(time)
    //     return thisTime.getTime() / 1000
    // }

    //授权门店 查询
    $searchOn.on('click', function () {
            var options = $('.time-type-on option:selected');
            str_starttimeOn = $('input[data-id="starttimeOn"]').val()
            str_endtimeOn = $('input[data-id="endtimeOn"]').val()
            str_storeTpl = $('#searchInputAccountOn').val()
            str_typeOn = options.val()
            getStoreInfoOn({
                account: $('#searchInputAccountOn').val(),
                start_time: $('input[data-id="starttimeOn"]').val(),
                end_time: $('input[data-id="endtimeOn"]').val(),
                type: options.val()
            });
        });

    function getStoreInfoOn(params) {
        var _default = {
            page_num: 1,
            count_per_page: 10,
            start_time: str_starttimeOn,
            end_time: str_endtimeOn,
            account: str_storeTpl,
            type: str_typeOn
        };

        $.extend(_default, params);

        requestUrl({
            url: '/store/get/get-accredit-all.do',
            data: _default,
            success: function (res) {
                if (res.status == 17056) {
                    salert("", res.data.errMsg, "warning", false);
                }
                var data = res.data;
                var num = data.total_count;

                if (num == null) {
                    num = 1
                }

                $('#J_store_box').html(juicer(_storeTpl, data.getaccreditall))
                pagingBuilder.build($('#J_pageOn_box'), _default.page_num, _default.count_per_page, num)
                pagingBuilder.click($('#J_pageOn_box'), function (page) {
                    getStoreInfoOn({page_num: page})
                })
            }
        });
    }

    getStoreInfoOn()

    //禁用
    $('#J_store_box').on('click', '.stop', function () {
        var store_id = $(this).attr('data-id')
        $('.stop-confirm').attr('data-id', store_id)
        $('#comment').val('')
    })

    $('.stop-confirm').on('click', function () {
        var store_id = $(this).attr('data-id')
        requestUrl({
            url: '/store/modify/forbid.do',
            type: 'POST',
            data: {
                id: store_id,
                comment: $('#comment').val()
            },
            success: function (res) {
                $('#comment_container').modal('hide')
                getStoreInfoOn()
                getStoreStopInfo()

            },
            error: function (err) {
                salert("", err.responseJSON.data.errMsg, "warning", false);
                $('#comment_container').modal('hide')
            }
        })
    })

    // 禁用门店 授权门店 查询
    $searchStop.on('click', function () {
        var options = $('.time-type-stop option:selected')
        str_starttimeStop = $('input[data-id="starttimeStop"]').val()
        str_endtimeStop = $('input[data-id="endtimeStop"]').val()
        str_typeStop = options.val()
        str_storeStopTpl = $('#searchInputAccountStop').val()

        getStoreStopInfo({
            account: $('#searchInputAccountStop').val(),
            start_time: $('input[data-id="starttimeStop"]').val(),
            end_time: $('input[data-id="endtimeStop"]').val(),
            type: options.val()
        })
    })

    // $('.search-account-stop').on('click', function () {
    //     getStoreStopInfo({
    //         account: $('#searchInputAccountOn').val()
    //     })
    // })

    function getStoreStopInfo(params) {
        var _default = {
            page_num: 1,
            count_per_page: 10,
            start_time: str_starttimeStop,
            end_time: str_endtimeStop,
            account: str_storeStopTpl,
            type: str_typeStop
        }
        $.extend(_default, params)
        requestUrl({
            url: '/store/get/get-forbidden-all.do',
            data: _default,
            success: function (res) {
                if (res.status == 17056) {
                    salert("", res.data.errMsg, "warning", false);
                }
                var data = res.data.getforbiddenall
                var Stopnum = res.data.total_count
                if (Stopnum == null) {
                    Stopnum = 1
                }
                $('#J_storeStop_box').html(juicer(_storeStopTpl, data))
                pagingBuilder.build($('#J_pageStop_box'), _default.page_num, _default.count_per_page, Stopnum)
                pagingBuilder.click($('#J_pageStop_box'), function (page) {
                    getStoreStopInfo({page_num: page})
                })
            }
        })
    }

    getStoreStopInfo()

    //启用
    $('#J_storeStop_box').on('click', '.re-using', function () {
        var data_id = $(this).attr('data-id')
        $('.on-confirm').attr('data-id',data_id)
    })

    $('.on-confirm').on('click', function () {
        requestUrl({
            url: '/store/modify/using.do',
            type: 'POST',
            data: {
                id: $(this).attr('data-id')
            },
            success: function () {
                $('#on_container').modal('hide')
                getStoreStopInfo()
                getStoreInfoOn()
            },
            error: function (err) {
                salert("", err.responseJSON.data.errMsg, "warning", false);
                $('#on_container').modal('hide')
            }
        })
    })


    //tab切换刷新数据

    $('a[data-toggle="tab"]').on('click', function () { 
        str_starttimeOn = ''
        str_endtimeOn = ''
        str_typeOn = ''
        str_storeTpl = ''
        
        str_starttimeStop = ''
        str_endtimeStop = ''
        str_typeStop = ''
        str_storeStopTpl = ''
        $('input[data-id="starttimeOn"]').val('')
        $('input[data-id="endtimeOn"]').val('');
        $('input[data-id="starttimeStop"]').val('')
        $('input[data-id="endtimeStop"]').val('')
        $('#searchInputAccountStop').val('')
        $('#searchInputAccountOn').val('')
        $('select').val('')
        if ($(this).text() == '授权门店') {
            getStoreInfoOn()
        } else {
            getStoreStopInfo()
        }
     })
});
