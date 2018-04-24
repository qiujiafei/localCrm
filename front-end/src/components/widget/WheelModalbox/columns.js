const columnsMap = {
  id: 'id',
  brand_name: '轮胎名称'
}

const columns = []

const exception = [
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
