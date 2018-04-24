const columnsMap = {
  service_name: '服务项目',
  specification: '规格',
  service_code: '编码',
  price: '销售价格',
  service_claasification_name: '所属分类',
  type: '自助项目',
  created_time: '创建时间',

  comment: '备注',
  created_by: '创建人',
  id: 'id',
  service_claasification_id: '服务项目分类ID',
  status: '状态',
  store_id: '门店ID'
}

const columns = []

const exception = [
  'created_by',
  'comment',
  'id',
  'service_claasification_id',
  'status',
  'store_id'
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
