const columnsMap = {
  name: '工种名称',
  created_by: '创建人',
  created_time: '创建时间',
  store_id: '门店',
  comment: '备注',

  id: 'id'
}

const columns = []
const exception = [
  'created_by',
  'store_id',
  'id'
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
