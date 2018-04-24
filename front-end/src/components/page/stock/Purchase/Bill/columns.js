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
    widgetPath: 'DepotPlainDropdown',
    required: true
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
      anothers: [ 'single_price' ],
      target: 'current_price'
    }
  },
  {
    Header: '采购单价',
    accessor: 'single_price',
    editable: true,
    formateFunc: 'formateToDecimal',
    required: true,
    fixed: true,
    operation: {
      type: 'multiplication',
      anothers: [ 'quantity' ],
      target: 'current_price'
    }
  },
  {
    Header: '采购金额',
    accessor: 'current_price',
    fixed: true,
    statistics: true,
    links: [ 'origin_price', [ 'settlement_price', 'discount' ] ]
  },
  {
    Header: '备注',
    accessor: 'comment',
    editable: true
  }
]
