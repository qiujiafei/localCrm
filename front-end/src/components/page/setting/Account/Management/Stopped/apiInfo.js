export default {
  data: {
    url: '/employeeuser/get/getpart.do',
    data: {
      count_per_page: 15,
      page_num: 1,
      keyword: '',
      status: 0
    },
    listKey: 'employeeuser',
    manipulationKey: 'id'
  },
  enable: {
    url: '/employeeuser/modify/start.do',
    data: {
      id: []
    },
    key: 'id'
  },
  deletion: {
    url: '/employeeuser/del/del.do',
    data: {
      id: []
    },
    key: 'id'
  }
}
