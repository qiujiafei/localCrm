const columnsMap = {
  id: 'id',
  main_name: '供应商名称',
  contact_name: '联系人',
  cell_number: '手机号码',
  phone_number: '联系电话',
  address: '联系地址',
  pay_method: '结算方式',
  bankaccountownner_name: '开户人姓名',
  bankcreateaccountbankname: '开户行',
  bankcardnumber: '银行卡账号',
  taxpayeridentificationnumber: '输入纳税人识别号',
  comment: '备注'
}

const columns = []

const exception = [
  'pay_method',
  'bankaccountownner_name',
  'bankcreateaccountbankname',
  'bankcardnumber',
  'taxpayeridentificationnumber',
  'id'
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
