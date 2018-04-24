export default [
  {
    id: 'add',
    name: '添加'
  },
  {
    id: 'disable',
    name: '员工离职',
    icon: 'delete'
  },
  {
    id: 'setting',
    name: '工种设置',
    onClick: pageInstance => {
      pageInstance.props.transferData({ tab: '工种设置', panel: pageInstance.props.mapInfo['工种设置'].component })
    }
  }
]
