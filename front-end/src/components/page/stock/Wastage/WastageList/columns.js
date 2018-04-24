const columnsMap = {
  number: '单号',
  quantity: '数量',
  total_price: '金额',
  damaged_by_name: '负责人',
  created_time: '报损时间',
  // store_name: '门店',
  comment: '备注',
  id: 'id'
}

const columns = []

const exception = [
  'id'
].join('')

const willSum = [
  'quantity',
  'total_price'
].join('')

for (const column in columnsMap) {
  const data = {
    Header: columnsMap[column],
    accessor: column
  }

  if (exception.indexOf(column) !== -1) {
    data.show = false
  }

  if (willSum.indexOf(column) !== -1) {
    data.displaySum = true
  }

  columns.push(data)
}

export default columns
