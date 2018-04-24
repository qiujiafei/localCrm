export default {
  data: {
    url: '/customerinfomation/get/getall.do',
    data: {
      count_per_page: 15,
      page_num: 1,
      keyword: '',
      status: '',
      is_member: ''
    },
    listKey: 'customerinfomation',
    manipulationKey: 'id'
  },
  modification: {
    url: '/customerinfomation/modify/modify.do',
    data: {},
    keyMap: {
      id: 'id'
    }
  },
  insertion: {
    url: '/customerinfomation/put/insert.do',
    data: {}
  },
  disable: {
    url: '/customerinfomation/modify/stop.do',
    data: {
      id: []
    },
    key: 'id'
  }
}
