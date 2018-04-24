export default {
  data: {
    url: '/employeetype/get/getall.do',
    data: {
      count_per_page: 15,
      page_num: 1
    },
    listKey: 'employeetype',
    manipulationKey: 'id'
  },
  insertion: {
    url: '/employeetype/put/insert.do',
    data: {}
  },
  modification: {
    url: '/employeetype/modify/modify.do',
    data: {},
    keyMap: {
      id: 'id',
      old_name: 'name'
    }
  },
  deletion: {
    url: '/employeetype/del/del.do',
    data: {
      id: []
    },
    key: 'id'
  }
}
