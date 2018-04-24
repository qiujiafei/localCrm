export default {
  data: {
    url: '/purchase/get/lists.do',
    data: {
      pageSize: 15,
      page: 1,
      startTime: '',
      endTime: '',
      supplier_id: ''
    },
    listKey: 'lists',
    manipulationKey: 'id'
  },
  deprecation: {
    url: '/purchase/modify/invalid.do',
    data: {
      id: []
    },
    key: 'id'
  }
}
