const columnsMap = {
  number: '单号',
  commodity_name: '商品名称',
  specification: ' 规格',
  commodity_code: '商品编码',
  quantity: '数量',
  cost_price: '采购单价',
  total_price: '合计',
  damaged_by_name: '负责人',
  default_depot_name: '仓库',
  created_time: '时间',
  // store_name: '门店',
  comment: '备注',
  id: 'id'
}

const columns = []

const exception = [
  'id',
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
