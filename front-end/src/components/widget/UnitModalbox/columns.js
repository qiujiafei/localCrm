const columnsMap = {
  id: 'id',
  unit_name: '单位名称',
  created_time: '创建时间'
}

const columns = []

const exception = [
  'id',
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
