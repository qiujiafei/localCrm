const columnsMap = {
  number: '单号',
  user_name: '盘点人',
  profit_loss: '盘盈或盘亏数量',
  diff_price: '盘盈或盘亏金额',
  created_time: '盘点时间',
  // comment: '备注',

  id: 'id'
}

const columns = []

const exception = [
  'id'
].join('')

const willSum = [
  // 'quantity',
  // 'total_price'
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
