const columnsMap = {
  number: '单号',
  quantity: '数量',
  total_price: '金额',
  picking_by_name: '领料员',
  created_time: '领料时间',
  store_name: '门店',
  // comment: '备注',

  id: 'id'
}

const columns = []

const exception = [
  'id',
  'store_name'
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
