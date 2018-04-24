export default [
  {
    Header: '商品',
    accessor: 'commodity_name'
  },
  {
    Header: '创建时间',
    accessor: 'created_time'
  },
  {
    Header: '商品编码',
    accessor: 'commodity_code'
  },
  {
    Header: '规格',
    accessor: 'specification'
  },
  {
    Header: '单位',
    accessor: 'unit_name'
  },
  {
    Header: '仓库',
    accessor: 'depot_name'
  },
  {
    Header: '库存数量',
    accessor: 'stock'
  },
  {
    Header: '盘点数量',
    accessor: 'quantity',
    editable: true,
    statistics: true
  },
  {
    Header: '盘点盈亏',
    accessor: 'result',
    statistics: true
  }
]
