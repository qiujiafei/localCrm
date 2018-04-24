const columnsMap = {
  addition_name: '项目名称',
  price: '价格',

  id: 'id',
  status: '状态',
  store_id: '仓库id',
  created_time: '创建日期',
  created_by: '创建人'
}

const columns = []

const exception = [
  'id',
  'status',
  'store_id',
  'created_time',
  'created_by'
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
