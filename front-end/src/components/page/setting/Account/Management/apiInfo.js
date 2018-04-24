export default {
  data: {
    url: '/employeeuser/get/getpart.do',
    data: {
      count_per_page: 15,
      page_num: 1,
      keyword: '',
      status: 1
    },
    listKey: 'employeeuser',
    manipulationKey: 'id'
  },
  insertion: {
    url: '/employeeuser/put/insert.do',
    data: {},
    alert: {
      msg: '新账号已生成',
      content: '',
      contentFromApiKey: 'account'
    }
  },
  disable: {
    url: '/employeeuser/modify/stop.do',
    data: {
      id: []
    },
    key: 'id'
  },
  modification: {
    url: '/employeeuser/modify/modify.do',
    data: {},
    keyMap: {
      id: 'id'
    }
  },
  deletion: {
    url: '/employeeuser/del/del.do',
    data: {
      id: []
    },
    key: 'id'
  }
}
