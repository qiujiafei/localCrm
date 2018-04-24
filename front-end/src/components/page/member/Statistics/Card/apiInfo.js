export default {
  url: '/customerinfomation/get/getmemberstatistics.do',
  data: {
    type: '',
    start_time: '',
    end_time: ''
  },
  defaultData: {
    member_count: {
      key: '会员',
      value: '0'
    },
    traveler_count: {
      key: '散客',
      value: '0'
    }
  }
}
