const columnsMap = {
  account: '单号',
  cardId: '卡号',
  carnum: '车牌号',
  name: '姓名',
  cars: '车系',
  mobile: '手机号',
  consumeproject: '消费项目',
  category: '分类',
  amount: '金额',
  paid: '支付金额',
  preferential: '优惠',
  personnel: '施工人员',
  end_time: '结算时间',
  create_time: '创建时间',
  amount_people: '结算人'
}

const tableColumns = []

const exception = [
  
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
