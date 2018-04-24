export default {
  data: {
    url: '/supplier/get/lists.do',
    data: {
      pageSize: 15,
      page: 1,
      searchCategory: '',
      searchKeys: ''
    },
    listKey: 'lists',
    manipulationKey: 'id'
  },
  insertion: {
    url: '/supplier/put/insert.do',
    data: {}
  },
  modification: {
    url: '/supplier/modify/edit.do',
    data: {},
    keyMap: {
      id: 'id',
      old_main_name: 'main_name'
    }
  },
  deletion: {
    url: '/supplier/delete/index.do',
    data: {
      pkids: []
    },
    key: 'pkIds'
  },
  export: {
    url: '/supplier/get/down.do',
    data: {
      names: []
    }
  }
}
