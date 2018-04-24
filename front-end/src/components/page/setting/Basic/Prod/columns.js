import React from 'react'

const columnsMap = {
  barcode: '条形码',
  commodity_code: '商品编码',
  commodity_name: '商品名称',
  specification: '规格',
  price: '售价',
  classification_name: '所属分类',
  unit_name: '单位',
  status: '状态',
  depot_name: '仓库',
  commodity_property_name: '配件属性',
  originate: '来源',
  comment: '备注',

  id: 'id'
}

const exception = [
  'depot_name',
  'commodity_property_name',
  'id'
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
