const apiInfo = {
  data: {
    url: '/classification/get/getall.do',
    data: {
      depth: [ 1 ]
    },
    manipulationKey: 'id'
  },
  insertion: {
    url: '/classification/put/insert.do',
    data: {}
  },
  modification: {
    url: '/classification/modify/modify.do',
    data: {},
    keyMap: {
      id: 'id'
    }
  },
  deletion: {
    url: '/classification/del/del.do',
    data: {
      id: []
    },
    key: 'id'
  }
}

export default apiInfo
