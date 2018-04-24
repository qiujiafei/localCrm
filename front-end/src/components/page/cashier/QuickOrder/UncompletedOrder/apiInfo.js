export default {
  data: {
    url: 'bill/get/get-no-account.do',
    data: {
      count_per_page: 15,
      page_num: 1,
      keyword: '',
      status: '',
      start_time: '',
      end_time: ''
    },
    listKey: 'getnoaccount',
    manipulationKey: 'id'
  },

  count: {
    url: '/bill/modify/account.do',
    data: {
      id: []
    },
    key: 'id'
  }
}
