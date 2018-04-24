export default {
  data: {
    url: '/service/get/getall.do',
    data: {
      count_per_page: 15,
      page_num: 1,
      keyword: '',
      service_claasification_id: '',
      status: '0'
    },
    listKey: 'service',
    manipulationKey: 'id'
  },
  insertion: {
    url: '/service/put/insert.do',
    data: {}
  },
  enable: {
    url: '/service/modify/open.do',
    data: {
      id: []
    },
    key: 'id'
  },
  deletion: {
    url: '/service/del/del.do',
    data: {
      id: []
    },
    key: 'id'
  },
  export: {
    url: '/service/get/getexport.do',
    data: {
      keyword: '',
      status: '0'
    }
  }
}
