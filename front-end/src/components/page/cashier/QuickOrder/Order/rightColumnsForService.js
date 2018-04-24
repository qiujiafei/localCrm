const columnsMap = {
  service_name: '服务项目',
  price: '金额',
  service_claasification_name: '分类',
  created_time: '创建时间',

  comment: '备注',
  created_by: '创建人',
  id: 'id',
  service_claasification_id: '服务项目分类ID',
  status: '状态',
  store_id: '门店ID',
  type: '自助项目',
  specification: '规格',
  service_code: '编码'
}

const columns = []

const exception = [
  'created_by',
  'comment',
  'id',
  'service_claasification_id',
  'status',
  'store_id',
  'type',
  'service_code',
  'specification'
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
