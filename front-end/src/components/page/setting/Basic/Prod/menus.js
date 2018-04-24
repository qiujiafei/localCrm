export default [
  {
    id: 'add',
    name: '新增'
  },
  {
    id: 'delete',
    name: '删除'
  },
  // {
  //   id: 'export',
  //   name: '导出'
  // },
  {
    id: 'group',
    name: '分类管理',
    onClick: pageInstance => {
      pageInstance.props.transferData({ tab: '分类管理', panel: pageInstance.props.mapInfo['分类管理'].component })
    }
  },
  {
    id: 'unit',
    name: '计量单位管理',
    onClick: pageInstance => {
      pageInstance.props.transferData({ tab: '计量单位管理', panel: pageInstance.props.mapInfo['计量单位管理'].component })
    }
  }
]
