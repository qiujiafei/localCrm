export default {
  data: {
    url: '/picking/get/getall.do',
    data: {
      count_per_page: 15,
      page_num: 1,
      number: ''
    },
    listKey: 'picking',
    manipulationKey: 'id'
  },
  deprecation: {
    url: '/picking/modify/invalid.do',
    data: {
      destroy: []
    },
    key: 'destroy',
    type: 'object'
  }
}
