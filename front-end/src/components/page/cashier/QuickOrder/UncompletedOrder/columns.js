import React from 'react'

const columnsMap = {
  bill_number: '开单号',
  customer_name: '客户名称',
  number: '车牌号码',
  brand_name: '车系',
  cellphone_number: '手机号码',
  service_name: '项目/商品',
  created_time: '创建时间',
  final_price: '支付金额',
  technician_name: '施工人员',
  comment: '备注'
}

const exception = [

].join('')

const columns = []

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



  columns.push(data)
}

export default columns
