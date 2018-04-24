
export default [
  {
    id: 'add',
    name: '新增'
  },
  {
    id: 'disable',
    name: '停用'
  },
  {
    id: 'checked',
    name: '停用列表',
    onClick: pageInstance => {
      pageInstance.props.transferData({ tab: '停用列表', panel: pageInstance.props.mapInfo['停用列表'].component })
    }
  }
  // {
  //   id: 'item-plus',
  //   name: '消费记录',
  //   onClick: pageInstance => {
  //     pageInstance.props.transferData({ tab: '消费记录', panel: pageInstance.props.mapInfo['消费记录'].component })
  //   }
  // }
]
