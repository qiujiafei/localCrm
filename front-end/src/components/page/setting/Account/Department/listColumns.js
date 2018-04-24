import React from 'react'

export default [
  {
    Header: null,
    accessor: 'selector',
    width: 40,
    resizable: false
  },
  {
    Header: 'ID',
    accessor: 'id',
    show: false
  },
  {
    Header: '账号',
    accessor: 'account',
    Cell: function Cell(row) { return <div title={ row.value }>{ row.value }</div> }
  },
  {
    Header: '员工姓名',
    accessor: 'name',
    Cell: function Cell(row) { return <div title={ row.value }>{ row.value }</div> }
  },
  {
    Header: '工种',
    accessor: 'employee_type_name',
    Cell: function Cell(row) { return <div title={ row.value }>{ row.value }</div> }
  }
]
