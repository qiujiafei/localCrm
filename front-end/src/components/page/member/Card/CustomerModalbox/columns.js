const columnsMap = {
  id: 'id',
  customer_name: '客户姓名',
  number: '车牌',
  cellphone_number: '手机号',
  brand_name: '车型'
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

  if (exception.indexOf(column) !== -1) {
    data.show = false
  }

  if (column === 'number') {
    data.Cell = function Cell(row) {
      return row.original.number_plate_province_name + row.original.number_plate_alphabet_name + row.original.number_plate_number
    }
  }

  columns.push(data)
}

export default columns
