export default {
  data: {
    url: '/depot/get/lists.do',
    data: {
      pageSize: 15,
      page: 1
    },
    listKey: 'lists',
    manipulationKey: 'id'
  },
  insertion: {
    url: '/depot/put/insert.do',
    data: {}
  },
  modification: {
    url: '/depot/modify/edit.do',
    data: {},
    keyMap: {
      id: 'id',
      old_depot_name: 'depot_name',
      old_store_id: 'store_id'
    }
  },
  deletion: {
    url: '/depot/delete/index.do',
    data: {
      id: ''
    },
    key: 'id'
  }
}
