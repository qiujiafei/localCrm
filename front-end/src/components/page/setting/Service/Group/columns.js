import React from 'react'

const columnsMap = {
  classification_name: '分类名称',
  created_time: '创建时间',
  created_by: '创建人',
  depth: '层级',
  id: 'ID',
  parent_id: '父ID',
  parent_name: '父级名称',
  status: '状态',
  comment: '备注'
}

const exception = [
  'created_by',
  'depth',
  'id',
  'parent_id',
  'parent_name',
  'status',
  'comment'
].join('')

const special = [
  'created_time'
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

  if (special.indexOf(column) !== -1) {
    data.Cell = function Cell(row) {
      return (
        <div data-cell={ column } title={ row.value }>
          { row.value }
        </div>
      )
    }
  }

  if (column === 'classification_name') {
    data.Cell = function Cell(row) {
      if (row.original.depth == 1) {
        row.row.interaction = null
      }

      return (
        <div
          style={ {
            textAlign: 'left'
          } }
          data-cell="classification_name"
          title={ row.value }
        >
          <i
            className={ row.original.end ? 'fa fa-file-o' : row.original.open ? 'fa fa-folder-open' : 'fa fa-folder' }
            data-id="expander"
            style={
              {
                display: 'inline-block',
                marginRight: '5px',
                verticalAlign: 'middle',
                color: '#337ab7',
                fontSize: '16px',
                paddingLeft: row.original.sub ? (row.original.depth - 1) * 15 + 'px' : 0
              }
            }
          ></i>
          <span style={ { verticalAlign: 'middle' } }>{ row.value }</span>
        </div>
      )
    }
  }

  columns.push(data)
}

export default columns
