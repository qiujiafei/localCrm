export default [
  {
    Header: '商品',
    accessor: 'commodity_name',
    widgetPath: 'ProdPlainDropdown',
    required: true
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
    accessor: 'depot_name',
    required: true
  },
  {
    Header: '采购单价',
    accessor: 'price'
  },
  {
    Header: '数量',
    accessor: 'quantity',
    editable: true,
    statistics: true,
    formateFunc: 'formateToInteger',
    required: true,
    operation: {
      type: 'multiplication',
      anothers: [ 'price' ],
      target: 'total_price'
    }
  },
  {
    Header: '金额合计',
    accessor: 'total_price',
    fixed: true,
    statistics: true
  },
  {
    Header: '备注',
    accessor: 'comment',
    editable: true
  }
]
