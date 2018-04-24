const columnsMap = {
  purchase_number: '单号',
  username: '采购员',
  commodity_name: '商品名称',
  quantity: '采购数量',
  current_price: '采购单价',
  // total_price: '本次付款',
  depot_name: '仓库',
  supplier_name: '供应商',
  created_time: '时间',
  comment: '备注',

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
