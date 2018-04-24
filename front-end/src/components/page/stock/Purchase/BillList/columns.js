import React from 'react'

const columnsMap = {
  number: '单号',
  quantity: '商品数量',
  total_price: '金额',
  discount: '优惠金额',
  purchaseUserName: '采购员',
  supplier_id: '供应商ID',
  supplierName: '供应商',
  created_time: '采购时间',
  status: '状态',
  comment: '备注',

  id: 'id'
}

const columns = []

const exception = [
  'store_id',
  'supplier_id',
  'id'
].join('')

const willSum = [
  'quantity',
  'total_price',
  'discount'
].join('')

for (const column in columnsMap) {
  const data = {
    Header: columnsMap[column],
    accessor: column
  }

  if (exception.indexOf(column) !== -1) {
    data.show = false
  }

  if (willSum.indexOf(column) !== -1) {
    data.displaySum = true
  }

  if (column === 'status') {
    data.Cell = function Cell(row) {
      const value = row.value == 0 ? '挂单' : '入库'

      return <div title={ value }>{ value }</div>
    }
  }

  columns.push(data)
}

export default columns
