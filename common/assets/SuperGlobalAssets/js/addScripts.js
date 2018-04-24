(function () {
    function addScripts(scripts, callback) {
        var count = 0;
        var fragment = document.createDocumentFragment()
        var head = document.getElementsByTagName('head')[0]
        var existedScripts = Array.prototype.slice.call(document.querySelectorAll('script'))

        existedScripts = existedScripts.map(function (script) {
            return script.src
        })

        for (var i = 0; i < scripts.length; i += 1) {
            var script = document.createElement('script')

            script.src = scripts[i]

            if (existedScripts.indexOf(script.src) !== -1) {
                continue
            }

            head.appendChild(script)

            script.onload = function () {
                handle()
            }
            script.onerror = function () {
                console.log(this)
                handle()
            }
        }

        function handle() {
            count += 1
            if (count === scripts.length) {
                callback()
            }
        }
    }

    window.apex = window.apex || {}
    if (!window.apex.addScripts) {
        window.apex.addScripts = addScripts
    }
}())
