import React from 'react'

const columnsMap = {
  name: '门店',
  phone_number: '电话',
  address: '地址',
  is_main_store: '是否总店'
}

const columns = []

const exception = [
  'is_main_store'
].join('')

for (const column in columnsMap) {
  const data = {
    Header: columnsMap[column],
    accessor: column
  }

  if (exception.indexOf(column) !== -1) {
    data.show = false
  }

  if (column === 'is_main_store') {
    data.Cell = function Cell(row) {
      if (row.value == 1) {
        return <div className="crm-color crm-color-primary" title="是">是</div>
      } else {
        return <div title="否">否</div>
      }
    }
  }

  columns.push(data)
}

export default columns
