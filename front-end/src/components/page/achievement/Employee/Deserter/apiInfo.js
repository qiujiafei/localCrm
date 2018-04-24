export default {
  data: {
    url: '/employee/get/getall.do',
    data: {
      count_per_page: 15,
      page_num: 1,
      keyword: '',
      store_id: '',
      status: 1
    },
    listKey: 'employee',
    manipulationKey: 'employee_number'
  },
  enable: {
    url: '/employee/modify/open.do',
    data: {
      employee_number: []
    },
    key: 'employee_number'
  },
  deletion: {
    url: '/employee/del/del.do',
    data: {
      employee_number: []
    },
    key: 'employee_number'
  }
}
