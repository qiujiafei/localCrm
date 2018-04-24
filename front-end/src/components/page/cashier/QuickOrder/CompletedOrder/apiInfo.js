export default {
  data: {
    url: '/bill/get/get-account.do',
    data: {
      count_per_page: 15,
      page_num: 1,
      keyword: '',
      status: '',
      start_time: '',
      end_time: ''
    },
    listKey: 'getaccount',
    manipulationKey: 'id'
  }
}
