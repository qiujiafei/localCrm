const menus = [
  {
    id: 'add',
    name: '添加'
  },
  {
    id: 'disable',
    name: '停用'
  },
  // {
  //   id: 'export',
  //   name: '导出'
  // },
  {
    id: 'group',
    name: '服务产品分类',
    onClick: pageInstance => {
      pageInstance.props.transferData({ tab: '服务产品分类', panel: pageInstance.props.mapInfo['服务产品分类'].component })
    }
  },
  {
    id: 'item-minus',
    name: '已停用项目',
    onClick: pageInstance => {
      pageInstance.props.transferData({ tab: '已停用项目', panel: pageInstance.props.mapInfo['已停用项目'].component })
    }
  }
  // {
  //   id: 'item-plus',
  //   name: '附加项目',
  //   onClick: pageInstance => {
  //     pageInstance.props.transferData({ tab: '附加项目', panel: pageInstance.props.mapInfo['附加项目'].component })
  //   }
  // }
]

export default menus
