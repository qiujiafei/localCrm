export default {
  data: {
    url: '/employee/get/getall.do',
    data: {
      count_per_page: 15,
      page_num: 1,
      keyword: '',
      store_id: '',
      status: 0
    },
    listKey: 'employee',
    manipulationKey: 'employee_number'
  },
  insertion: {
    url: '/employee/put/insert.do',
    data: {}
  },
  modification: {
    url: '/employee/modify/modify.do',
    data: {},
    keyMap: {
      employee_number: 'employee_number'
    }
  },
  disable: {
    url: '/employee/modify/leave.do',
    data: {
      employee_number: []
    },
    key: 'employee_number'
  }
}
