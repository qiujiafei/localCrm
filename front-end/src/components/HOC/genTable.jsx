import React, { Component } from 'react'
import ReactTable, { ReactTableDefaults } from 'react-table'

function genTable(options) {

  let {
    pageSize = 15,
    columns = [],
    data = []
  } = options

  return class Table extends Component {
    constructor(props) {
      super(props)
    }

    render() {
      return (
        <ReactTable
          showPagination={ false }
          noDataText={ '暂无数据' }
          className="crm-table -striped -highlight"
          defaultPageSize={ pageSize }
          sortable={ false }
          column={
            Object.assign({}, ReactTableDefaults.column, {
              headerClassName: 'crm-table-header',
              className: 'crm-table-cell'
            })
          }
          columns={ columns }
          data={ data }
          { ...this.props }
        />
      )
    }
  }
}

export default genTable
