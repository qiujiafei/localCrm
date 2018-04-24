(function () {
    function uploadImg(imgFile, opt) {
        getPermission(imgFile, function (permissionData) {
            var formData = new FormData()
            var xhr = new XMLHttpRequest()

            for (var i in permissionData) {
                formData.append(i, permissionData[i])
            }
            formData.append('file', imgFile)

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

    function getPermission(imgFile, callback) {
        var suffix = imgFile.name.match(/\.(.*)$/)[1]
        var xhr = new XMLHttpRequest()

        xhr.onreadystatechange = handler
        xhr.open('GET', '/site/carousel/get-oss-permission?' + [
            'file_suffix=' + suffix
        ].join('&'))
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

    window.apex = window.apex || {}
    if (!window.apex.uploadImg) {
        window.apex.uploadImg = uploadImg
    }
}());
