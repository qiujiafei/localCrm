(function () {
    function ApexExpressDropDownList() {
        this.version = '0.0.1'
        this.container = null
        this.containerWidthSearch = null
        this.storagedData = null
        this.initDropDownList = initDropDownList
        this.initDropDownListWidthSearch = initDropDownListWidthSearch
        this.removeDropDownList = removeDropDownList
    }
    function getExpressData(callback) {
        var xhr = new XMLHttpRequest()

        xhr.onreadystatechange = handler
        xhr.open('GET', '/order/express')
        xhr.send(null)

        function handler() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200 || xhr.status === 304) {
                    var JSONFormatedData = JSON.parse(xhr.responseText)
                    callback(JSONFormatedData.data)
                }
            }
        }
    }
    function getHost(callback) {
        var xhr = new XMLHttpRequest()

        xhr.onreadystatechange = handler
        xhr.open('GET', '/api-hostname')
        xhr.send(null)

        function handler() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200 || xhr.status === 304) {
                    var JSONFormatedData = JSON.parse(xhr.responseText)
                    callback(JSONFormatedData.data.hostname)
                }
            }
        }
    }
    function getExpressDataFromAPI(host, callback) {
        var xhr = new XMLHttpRequest()

        xhr.onreadystatechange = handler
        xhr.open('GET', host + '/express/get-company')
        xhr.send(null)

        function handler() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200 || xhr.status === 304) {
                    var JSONFormatedData = JSON.parse(xhr.responseText)
                    callback(JSONFormatedData.data)
                }
            }
        }
    }
    function initDropDownList(container, handleClick) {
        if (this.container) {
            removeDropDownList()
        }

        this.container = container

        getExpressData(function (data) {
            if (typeof container.innerHTML !== 'undefined') {
                container.innerHTML = tplForDropDownList(data)
                container.onclick = function (e) {
                    e.stopPropagation()

                    if (e.target.tagName === 'LI' && e.target.getAttribute('data-id')) {
                        handleClick({
                            id: e.target.getAttribute('data-id'),
                            name: e.target.innerHTML
                        })
                    }

                    if (e.target.getAttribute('data-type') === 'drawer') {
                        var element = document.querySelector('ul[data-id="drawer"]')
                        var titleElement = document.querySelector('dt[data-id="drawer"]')
                        var btnElement = document.querySelector('div[data-type="drawer"]')

                        if (element.style.display === 'none') {
                            element.style.display = 'block'
                            titleElement.style.display = 'block'
                            btnElement.innerHTML = '收起<i class="fa fa-angle-double-up" aria-hidden="true"></i>'
                        } else {
                            element.style.display = 'none'
                            titleElement.style.display = 'none'
                            btnElement.innerHTML = '展开<i class="fa fa-angle-double-down" aria-hidden="true"></i>'
                        }
                    }
                }
            }
        })
    }
    function initDropDownListWidthSearch(container, handleClick) {
        var _this = this

        _this.containerWidthSearch = container

        getHost(function (host) {
            getExpressDataFromAPI(host, function (data) {
                _this.storagedData = data
                container.innerHTML = tplForDropDownListWidthSearch(tplForSearchResult(data))
                container.onclick = function (e) {
                    e.stopPropagation()

                    if (e.target.tagName === 'LI' && e.target.getAttribute('data-id')) {
                        handleClick({
                            id: e.target.getAttribute('data-id'),
                            name: e.target.getAttribute('data-name')
                        })
                    }
                }
                container.querySelector('.search > input').onkeyup = function(e) {
                    if (_this.timer) {
                        clearTimeout(_this.timer)
                    }
                    _this.timer = setTimeout(function () {
                        var resultData = search(_this.storagedData, e.target.value)
                        container.querySelector('.result').innerHTML = tplForSearchResult(resultData)
                    }, 500)
                }
            })
        })
    }
    function removeDropDownList() {
        if (this.container && typeof this.container.innerHTML !== 'undefined') {
            this.container.innerHTML = ''
        }
        if (this.containerWidthSearch && typeof this.containerWidthSearch.innerHTML !== 'undefined') {
            this.containerWidthSearch.innerHTML = ''
        }
    }
    function tplForDropDownListWidthSearch(searchResult) {
        var tpl = ''

        tpl += '<div class="dropdown-list-with-search-for-express">'
        tpl += '<div class="search">'
        tpl += '<input type="text" />'
        tpl += '<i class="fa fa-search" aria-hidden="true"></i>'
        tpl += '</div>'
        tpl += '<div class="result">'

        tpl += searchResult

        tpl += '</div>'
        tpl += '</div>'

        return tpl
    }
    function tplForSearchResult(data) {
        var tpl = ''

        if (data.length > 0) {
            var groupByData = groupByAlphabet(data)

            tpl = '<ul>'

            for (var i in groupByData) {
                tpl += '<li>'
                tpl += '<span>' + i + '</span>'
                tpl += '<ul>'

                for (var j = 0; j < groupByData[i].length; j++) {
                    if (groupByData[i][j].formattedName) {
                        tpl += '<li data-id="' + groupByData[i][j].id + '" data-name="' + groupByData[i][j].name + '">' + groupByData[i][j].formattedName + '</li>'
                    } else {
                        tpl += '<li data-id="' + groupByData[i][j].id + '" data-name="' + groupByData[i][j].name + '">' + groupByData[i][j].name + '</li>'
                    }
                }

                tpl += '</ul>'
                tpl += '</li>'
            }

            tpl += '</ul>'
        } else {
            tpl += '<table><tr><td>暂无数据</td></tr></table>'
        }

        return tpl
    }
    function tplForDropDownList(data) {
        var tpl = '<div class="dropdown-list-for-express">'

        tpl += '<dl>'

        tpl += '<dt>常用物流<a href="/express">点此设置</a></dt>'
        tpl += '<dd>'
        tpl += '<ul>'

        for (var i = 0; i < data.common.length; i++) {
            tpl += '<li data-id="' + data.common[i].id + '">' + data.common[i].name + '</li>'
        }

        tpl += '</ul>'
        tpl += '</dd>'

        var groupByData = groupByAlphabet(data.items)

        tpl += '<dt data-id="drawer" style="display: none;">全部物流</dt>'
        tpl += '<dd>'
        tpl += '<ul data-id="drawer" style="display: none;">'

        for (var i in groupByData) {
            tpl += '<li>'
            tpl += '<span>' + i + '</span>'
            tpl += '<ul>'

            for (var j = 0; j < groupByData[i].length; j++) {
              tpl += '<li data-id="' + groupByData[i][j].id + '">' + groupByData[i][j].name + '</li>'
            }

            tpl += '</ul>'
            tpl += '</li>'
        }

        tpl += '</ul>'
        tpl += '</dd>'

        tpl += '</dl>'

        tpl += '<div data-type="drawer">展开<i class="fa fa-angle-double-down" aria-hidden="true"></i></div>'

        tpl += '</div>'

        return tpl
    }
    function groupByAlphabet(items) {
        var groupByData = {}

        for (var i = 0; i < items.length; i++) {
            var index = items[i].first_char

            if (!groupByData[index]) {
                groupByData[index] = []
            }

            groupByData[index].push(items[i])
        }

        return groupByData
    }
    function search(data, name) {
        var result = []

        for (var i in data) {
            var item = data[i]

            if (item.name.indexOf(name) !== -1) {
                var reg = new RegExp(name, 'g')

                item.formattedName = item.name.replace(reg, function (matched) {
                    return '<span style="color: #f00">' + matched + '</span>'
                })

                result.push(item)
            }
        }

        return result
    }

    window.apex = window.apex || {}
    if (!window.apex.apexExpressDropDownList) {
        window.apex.apexExpressDropDownList = new ApexExpressDropDownList()
        window.onclick = function () {
            apex.apexExpressDropDownList.removeDropDownList()
        }
    }
}());
