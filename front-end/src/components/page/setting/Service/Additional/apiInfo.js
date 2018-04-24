export default {
  data: {
    url: '/serviceaddition/get/getall.do',
    data: {
      count_per_page: 15,
      page_num: 1,
      keyword: ''
    },
    listKey: 'service_addition',
    manipulationKey: 'id'
  },
  insertion: {
    url: '/serviceaddition/put/insert.do',
    data: {}
  },
  modification: {
    url: '/serviceaddition/modify/modify.do',
    data: {},
    keyMap: {
      id: 'id'
    }
  },
  deletion: {
    url: '/serviceaddition/del/del.do',
    data: {
      id: []
    },
    key: 'id'
  }
}
