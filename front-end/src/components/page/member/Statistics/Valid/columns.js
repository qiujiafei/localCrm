const columnsMap = {
  customer_name: '客户姓名',
  alphabet_name: '车牌号码',
  cellphone_number: '手机号',
  brand_name: '车型',
  card_number: '卡号',
  type: '卡类型',
  price: '充值金额',
  // category: '消费次数',
  created_time: '办卡日期',
  comment: '备注',
  created_name: '经办人',
  id: '',
  number_plate_alphabet_name:'',
  number_plate_number: '',
  number_plate_province_name: '',
  style_name: ''
}

const tableColumns = []

const exception = [
  'id',
  'number_plate_alphabet_name',
  'number_plate_province_name',
  'number_plate_number',
  'style_name'
].join('')

for (const column in columnsMap) {
  const data = {
    Header: columnsMap[column],
    accessor: column
  }

  if (exception.indexOf(column) !== -1) {
    data.show = false
  }

  tableColumns.push(data)
}

export default tableColumns
