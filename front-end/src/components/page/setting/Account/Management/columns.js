const columnsMap = {
  id: '员工编号',
  account: '账号',
  account_name: '账号名称',
  name: '员工姓名',
  created_time: '创建时间',
  last_modified_time: '最后修改时间',
  status: '状态'
}

const tableColumns = []

const exception = [
  'id',
  'last_modified_time',
  'status'
].join('')

for (const column in columnsMap) {
  const data = {
    Header: columnsMap[column],
    accessor: column
  }

  if (exception.indexOf(column) !== -1) {
    data.show = false
  }

  tableColumns.push(data)
}

export default tableColumns
