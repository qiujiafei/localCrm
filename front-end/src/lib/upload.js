import ajax from './ajax'

function upload(file) {
  return getPermission(file)
}

function getPermission(file) {
  return ajax({
    method: 'GET',
    url: '/ossImage/get/oss-permission.do',
    data: {
      file_suffix: file.extName
    }
  }).then(info => {
    if (info.err) {
      return info
    } else {
      info.data.file = file
      return ajax({
        method: 'POST',
        url: info.data.host,
        data: formatFormData(info.data)
      }).then(info => {
        if (info.err) {
          return info
        } else {
          return info.data
        }
      })
    }
  })
}

function formatFormData(data) {
  const formData = new FormData()

  for (const key in data) {
    formData.append(key, data[key])
  }

  return formData
}

export default upload
