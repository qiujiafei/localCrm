export default {
  data: {
    url: '/commodityUnit/get/getall.do',
    data: {
      count_per_page: 15,
      page_num: 1
    },
    listKey: 'unit',
    manipulationKey: 'id'
  },
  insertion: {
    url: '/commodityUnit/put/insert.do',
    data: {}
  },
  modification: {
    url: '/commodityUnit/modify/modify.do',
    data: {},
    keyMap: {
      id: 'id',
      old_unit_name: 'unit_name'
    }
  },
  deletion: {
    url: '/commodityUnit/del/del.do',
    data: {
      id: []
    },
    key: 'id'
  }
}
