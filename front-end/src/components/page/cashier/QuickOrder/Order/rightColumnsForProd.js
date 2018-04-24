import React from 'react'

const columnsMap = {
  commodity_name: '商品名称',
  price: '采购价',
  default_depot_name: '仓库',
  stock: '库存',
  unit_name: '单位',
  created_time: '创建时间'
}

const exception = [
  'default_depot_name',
  'commodity_property_name',
  'barcode',
  'commodity_code',
  'specification',
  'comment',
  'originate',
  'commodity_property_name',
  'status'
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

  if (column === 'status') {
    data.Cell = function Cell(row) {
      if (row.value == 1) {
        return <div title="正常">正常</div>
      } else {
        return <div className="crm-color crm-color-error" title="停用">停用</div>
      }
    }
  }

  columns.push(data)
}

export default columns
