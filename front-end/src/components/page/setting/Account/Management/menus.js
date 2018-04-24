export default [
  {
    id: 'add',
    name: '新增'
  },
  {
    id: 'disable',
    name: '停用',
    icon: 'disable'
  },
  {
    id: 'disableAccount',
    name: '已停用账号',
    icon: 'disabled-user',
    onClick: pageInstance => {
      pageInstance.props.transferData({ tab: '已停用账号', panel: pageInstance.props.mapInfo['已停用账号'].component })
    }
  }
]
