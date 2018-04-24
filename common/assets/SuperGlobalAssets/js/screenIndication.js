;(function () {
    function ScreenIndication() {
        this.id = 'screenIndication'
        this.show = show
        this.hide = hide
    }

    function show() {
        var tpl = createTpl(this.id)
        var tmpDiv = document.createElement('div')

        tmpDiv.innerHTML = tpl
        document.body.appendChild(tmpDiv.firstChild)
    }

    function hide() {
        document.body.removeChild(document.getElementById(this.id))
    }

    function createTpl(id) {
        var style = {
            'position': 'fixed',
            'top': '0',
            'left': '0',
            'width': '100%',
            'height': '100%',
            'background-color': 'rgba(255,255,255,.6)'
        }

        var formatedStyle = ''

        for (var key in style) {
            formatedStyle += key + ':' + style[key] + ';'
        }

        var tpl = '<div id="' + id + '" style="' + formatedStyle + '"></div>'
        return tpl
    }

    window.apex = window.apex || {}
    if (!window.apex.screenIndication) {
        window.apex.screenIndication = new ScreenIndication()
    }
}());
