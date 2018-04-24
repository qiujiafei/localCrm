import React from 'react'

const columnsMap = {
  customer_name: '顾客姓名',
  gender: '性别',
  cellphone_number: '手机',
  customer_origination: '客户来源',
  consume_count: '消费次数',
  total_consume_price: '累计消费',
  number: '车牌',
  frame_number: '车架号',
  brand_name: '车型',
  // insurance_expire: '保险到期',
  is_member: '是否会员',
  created_name: '创办人',
  created_time: '创建时间',
  last_modified_time: '停用时间',
  comment: '备注',

  id: 'id'
}

const tableColumns = []

const exception = [
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

  if (column === 'number') {
    data.Cell = function Cell(rowInfo) {
      const { original } = rowInfo
      const value = original.number_plate_province_name + original.number_plate_alphabet_name + original.number_plate_number
      return <div title={ value }>{ value }</div>
    }
  }

  if (column === 'gender') {
    data.Cell = function Cell(rowInfo) {
      const { original } = rowInfo
      const value = [ '女', '男' ][original.gender]
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

  tableColumns.push(data)
}

export default tableColumns
