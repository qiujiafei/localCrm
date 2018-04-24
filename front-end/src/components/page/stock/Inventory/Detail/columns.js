const columnsMap = {
  number: '盘点单号',
  commodity_name: '商品名称',
  specification: '规格',
  barcode: '商品编码',
  quantity: '当前库存数量',
  inventory_quantity: '盘点前数量',
  profit_loss: '盘亏或盘盈数量',
  diff_price: '盘亏或盘盈金额',
  depot_name: '仓库',
  created_time: '盘点时间',
  id: 'id'
}

const columns = []

const exception = [
  'id'
].join('')

const willSum = [
  'diff_price'
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
