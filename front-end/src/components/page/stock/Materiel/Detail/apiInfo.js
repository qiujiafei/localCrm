export default {
  data: {
    url: '/pickingcommodity/get/getall.do',
    data: {
      count_per_page: 15,
      page_num: 1,
      commodity_name: '',
      number: ''
    },
    listKey: 'picking_commodity',
    manipulationKey: 'id'
  },
  disable: {
    url: '/picking/modify/invalid.do',
    data: {
      destroy: []
    },
    key: 'destroy'
  }
}
