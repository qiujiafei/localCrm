const columnsMap = {
  id: 'id',
  depot_name: '仓库名称',
  created_time: '创建时间',
  comment: '备注'
}

const columns = []

const exception = [
  'id',
  'created_time',
  'comment'
].join('')

for (const column in columnsMap) {
  const data = {
    Header: columnsMap[column],
    accessor: column
  }

  if (exception.indexOf(column) !== -1) {
    data.show = false
  }

  columns.push(data)
}

export default columns
