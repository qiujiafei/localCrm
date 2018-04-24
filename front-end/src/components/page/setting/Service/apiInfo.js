export default {
  data: {
    url: '/service/get/getall.do',
    data: {
      count_per_page: 15,
      page_num: 1,
      keyword: '',
      status: '1'
    },
    listKey: 'service',
    manipulationKey: 'id'
  },
  insertion: {
    url: '/service/put/insert.do',
    data: {}
  },
  modification: {
    url: '/service/modify/modify.do',
    data: {},
    keyMap: {
      id: 'id'
    }
  },
  disable: {
    url: '/service/modify/stop.do',
    data: {
      id: []
    },
    key: 'id'
  },
  export: {
    url: '/service/get/getexport.do',
    data: {
      keyword: '',
      status: ''
    }
  }
}
