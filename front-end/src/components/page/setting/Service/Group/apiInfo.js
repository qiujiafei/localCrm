const apiInfo = {
  data: {
    url: '/serviceclassification/get/getall.do',
    data: {
      depth: [ 1 ]
    },
    manipulationKey: 'id'
  },
  insertion: {
    url: '/serviceclassification/put/insert.do',
    data: {}
  },
  modification: {
    url: '/serviceclassification/modify/modify.do',
    data: {},
    keyMap: {
      id: 'id'
    }
  },
  deletion: {
    url: '/serviceclassification/del/del.do',
    data: {
      id: []
    },
    key: 'id'
  }
}

export default apiInfo
