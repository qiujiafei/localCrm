export default {
  data: {
    url: '/commodityManage/get/getall.do',
    data: {
      count_per_page: 15,
      page_num: 1,
      keyword: '',
      status: '',
      classification_name: '',
      store_id: ''
    },
    listKey: 'commodity',
    manipulationKey: 'id'
  },
  insertion: {
    url: '/commodityManage/put/insert.do',
    data: {}
  },
  modification: {
    url: '/commodityManage/modify/update.do',
    data: {},
    keyMap: {
      origin_name: 'commodity_name',
      origin_barcode: 'barcode'
    }
  },
  deletion: {
    url: '/commodityManage/delete/batch.do',
    data: {
      commoditys: []
    },
    key: 'commoditys'
  },
  export: {
    url: '/commodityManage/get/getexport.do'
  }
}
