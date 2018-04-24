import React from 'react'

const columnsMap = {
  id: 'id',
  card_number: '卡号',
  customer_name: '姓名',
  cellphone_number: '手机',
  brand_name: '品牌车系',
  frame_number: '车架号',
  number: '车牌',
  customer_origination: '客户来源',
  is_member: '是否会员'
}

const columns = []

const exception = [
  'id'
].join('')

for (const column in columnsMap) {
  const data = {
    Header: columnsMap[column],
    accessor: column
  }

  if (column === 'number') {
    data.Cell = function Cell(rowInfo) {
      const { original } = rowInfo
      const value = original.number_plate_province_name + original.number_plate_alphabet_name + original.number_plate_number
      return <div title={ value }>{ value }</div>
    }
  }

  if (exception.indexOf(column) !== -1) {
    data.show = false
  }

  if (column === 'is_member') {
    data.Cell = function Cell(rowInfo) {
      const { original } = rowInfo
      const value = [ '否', '是' ][original.is_member]
      return <div title={ value }>{ value }</div>
    }
  }

  columns.push(data)
}

export default columns
