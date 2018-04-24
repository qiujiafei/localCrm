export default {
  data: {
    url: ' /customerinfomation/get/getallmember.do',
    data: {
      count_per_page: 15,
      page_num: 1,
      start_time:'',
      end_time: ''
    },
    listKey: 'customerinfomation',
    manipulationKey: 'id'
  },
  disable: {
    url: ' /customerinfomation/modify/nomember.do',
    data: {
      id: []
    },
    key: 'id'
  }
}
