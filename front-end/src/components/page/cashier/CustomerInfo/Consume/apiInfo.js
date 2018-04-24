export default {
  data: {
    url: '/customerinfomation/get/getall.do',
    data: {
      count_per_page: 15,
      page_num: 1,
      keyword: '',
      status: 0,
      is_member: ''
    },
    listKey: 'customerinfomation',
    manipulationKey: 'customerinfomation'
  },
  enable: {
    url: '/customerinfomation/modify/open.do',
    data: {
      id:[]
    },
    key: 'id'
  }
}