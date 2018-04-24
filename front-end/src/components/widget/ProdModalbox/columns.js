const columnsMap = {
  commodity_id: 'id',
  commodity_name: '商品名称',
  specification: '规格',
  commodity_code: '商品编码',
  stock: '库存',
  price: '采购单价',
  depot_name: '仓库',
  created_time: '创建时间'
}

const columns = []

const exception = [
  'commodity_id'
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
