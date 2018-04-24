const columnsMap = {
  employee_number: '员工编号',
  name: '员工姓名',
  phone_number: '手机号码',
  employee_type_name: '工种',
  basic_salary: '底薪',
  ID_code: '身份证',
  store_id: '所属门店',
  created_time: '添加时间',
  ID_card_image: '身份证图片',
  ability: '能力值',
  attendance_code: '打卡密码',
  comment: '备注',
  created_by: '添加人',
  qq_number: 'QQ号',
  status: '状态'
}

const tableColumns = []

const exception = [
  'store_id',
  'status',
  'qq_number',
  'created_time',
  'created_by',
  'comment',
  'ID_card_image',
  'ability',
  'attendance_code'
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
