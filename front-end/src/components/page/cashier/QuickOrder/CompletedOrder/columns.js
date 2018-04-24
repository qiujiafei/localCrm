
import React from 'react'

const columnsMap = {
  bill_number: '单号',
  frame_number: '车架号',
  number: '车牌号',
  customer_name: '姓名',
  brand_name: '车系',
  cellphone_number: '手机号',
  service_name: '服务项目',
  is_member: '是否会员',
  member_discount: '优惠',
  price: '施工金额',
  final_price: '支付金额',
  technician_name: '施工人员',
  created_time: '创建时间',
  last_modified_time: '结算时间',
  comment: '备注',
  created_name: '结算人'
}

const exception = [

].join('')

const tableColumns = []

for (const column in columnsMap) {
  const data = {
    Header: columnsMap[column],
    accessor: column
  }

  if (exception.indexOf(column) !== -1) {
    data.show = false
  }

  if (column === 'number') {
    data.Cell = function Cell(rowInfo) {
      const { original } = rowInfo
      const value = original.number_plate_province_name + original.number_plate_alphabet_name + original.number_plate_number
      return <div title={ value }>{ value }</div>
    }
  }

  if (column === 'is_member') {
    data.Cell = function Cell(rowInfo) {
      const { original } = rowInfo
      const value = [ '否', '是' ][original.is_member]
      return <div title={ value }>{ value }</div>
    }
  }

  if (column === 'service_name'){
    data.Cell = function Cell(rowInfo){
      const { original } = rowInfo;
      const service_name = original.service_name.join(',')
      return <div title={ service_name }>{ service_name }</div>
    }
  }

  tableColumns.push(data)
}

export default tableColumns
