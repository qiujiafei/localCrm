import 'whatwg-fetch'

function buildParam(prefix, obj, add) {
  if (Array.isArray(obj)) {
    obj.forEach((value, index) => {
      if (/\[\]$/.test(prefix)) {
        add(prefix, value)
      } else {
        buildParam(prefix + '[' + (typeof value === 'object' && value != null ? index : '') + ']', value, add)
      }
    })
  } else if (typeof obj === 'object') {
    for (const name in obj) {
      buildParam(prefix + '[' + name + ']', obj[name], add)
    }
  } else {
    add(prefix, obj)
  }
}

function formatParam(data) {
  const s = []

  if (Array.isArray(data)) {
    data.forEach((value, key) => {
      add(key, value)
    })
  } else {
    for (const prefix in data) {
      buildParam(prefix, data[prefix], add)
    }
  }

  function add(key, valueOfFunction) {
    const value = typeof valueOfFunction === 'function' ? valueOfFunction() : valueOfFunction
    s[s.length] = encodeURIComponent(key) + '=' + encodeURIComponent(value == null ? '' : value)
  }

  return s.join('&')
}

function request(url, opt) {
  return fetch(url, opt)
    .then(res => {
      let result = null

      if (res.status == 200) {
        result = res.json()
      } else {
        let commonData = {
          err: true,
          status: res.status,
          desc: '',
          goToLogin: false,
          http: true
        }

        switch (res.status) {
          case 403:
            commonData = res.json()
            break

          case 404:
            commonData.desc = '接口去火星了旅游了'
            break

          case 500:
            commonData.desc = '服务器内部错误'
            break

          case 504:
            commonData.desc = '服务器已然沉睡'
            break

          // 没有 default
        }

        result = commonData
      }

      return result
    })
    .then(info => {

      if (info.status != 200 && !info.http) {
        const thisData = {
          err: false,
          status: info.status,
          desc: info.data.errMsg,
          goToLogin: false,
          api: true
        }

        thisData.err = true

        if (info.data.errMsg.indexOf('未登录') !== -1) {
          thisData.goToLogin = true
        }

        info = thisData
      }

      return info
    })
}

function ajax({ method = 'GET', url, data = {} }) {

  // 所有接口必须带 token
  data.token = localStorage.getItem('9DAYE_CRM_TOKEN')

  const formatedData = data.constructor === FormData ? data : formatParam(data)
  const opt = { method }

  if (method === 'POST' && data.constructor !== FormData) {
    opt.headers = { 'Content-Type': 'application/x-www-form-urlencoded' }
    opt.body = formatedData
  } else {
    if (data.constructor === FormData) {
      opt.body = formatedData
    } else {
      url += '?' + formatedData
    }
  }

  return request(url, opt)
}

export default ajax
