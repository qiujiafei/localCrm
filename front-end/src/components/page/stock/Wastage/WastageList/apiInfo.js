export default {
  data: {
    url: '/damaged/get/getall.do',
    data: {
      count_per_page: 15,
      page_num: 1,
      number: ''
    },
    listKey: 'damaged',
    manipulationKey: 'id'
  },
  disable: {
    url: '/damageddestroy/get/getall.do',
    data: {
      destroy: []
    },
    key: 'destroy'
  }
}