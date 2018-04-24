var mapInfo = {
  '首页': {
    dirname: 'Index/Home',
    name: 'Home'
  },

  // 设置
  '基础设置': {
    dirname: 'setting/Basic',
    name: 'Basic'
  },
  '供应商管理': {
    dirname: 'setting/Basic/Supply',
    name: 'Supply',
    iconClass: 'user'
  },
  '商品信息管理': {
    dirname: 'setting/Basic/Prod',
    name: 'Prod',
    iconClass: 'paper'
  },
  '分类管理': {
    dirname: 'setting/Basic/Prod/Group',
    name: 'Group',
    iconClass: 'group'
  },
  '计量单位管理': {
    dirname: 'setting/Basic/Prod/Unit',
    name: 'Unit',
    iconClass: 'unit'
  },
  '仓库管理': {
    dirname: 'setting/Basic/Store',
    name: 'Store',
    iconClass: 'store'
  },
  '服务项目': {
    dirname: 'setting/Service',
    name: 'Service'
  },
  '已停用项目': {
    dirname: 'setting/Service/Stopped',
    name: 'Stopped'
  },
  '附加项目': {
    dirname: 'setting/Service/Additional',
    name: 'Additional'
  },
  '服务产品分类': {
    dirname: 'setting/Service/Group',
    name: 'Group'
  },
  '门店管理': {
    dirname: 'setting/Shop',
    name: 'Shop'
  },
  '权限设置': {
    dirname: 'setting/Account',
    name: 'Account'
  },
  '账号管理': {
    dirname: 'setting/Account/Management',
    name: 'Management'
  },
  '部门权限': {
    dirname: 'setting/Account/Department',
    name: 'Department'
  },
  '已停用账号': {
    dirname: 'setting/Account/Management/Stopped',
    name: 'Stopped'
  },
  '导入数据': {
    dirname: 'setting/Import',
    name: 'Import'
  },
  '导入客户资料': {
    dirname: 'setting/Import',
    name: 'Custom'
  },
  '导入商品信息': {
    dirname: 'setting/Import',
    name: 'Prod'
  },
  '导入服务项目': {
    dirname: 'setting/Import',
    name: 'Service'
  },

  // 绩效
  '员工管理': {
    dirname: 'achievement/Employee',
    name: 'Employee'
  },
  '在职员工': {
    dirname: 'achievement/Employee/Fighter',
    name: 'Fighter'
  },
  '离职员工': {
    dirname: 'achievement/Employee/Deserter',
    name: 'Deserter'
  },
  '工种设置': {
    dirname: 'achievement/Employee/Fighter/Profession',
    name: 'Profession'
  },

  // 会员
  '会员办卡': {
    dirname: 'member/Card',
    name: 'Card'
  },
  '会员统计': {
    dirname: 'member/Statistics',
    name: 'Statistics'
  },
  '会员卡统计': {
    dirname: 'member/Statistics/Card',
    name: 'Card'
  },
  '有效会员': {
    dirname: 'member/Statistics/Valid',
    name: 'Valid'
  },

  // 库存
  '采购入库': {
    dirname: 'stock/Purchase',
    name: 'Purchase'
  },
  '采购统计': {
    dirname: 'stock/Purchase/Statistics',
    name: 'Statistics'
  },
  '采购单': {
    dirname: 'stock/Purchase/Bill',
    name: 'Bill'
  },
  '采购单据': {
    dirname: 'stock/Purchase/BillList',
    name: 'BillList'
  },
  '采购明细': {
    dirname: 'stock/Purchase/Detail',
    name: 'Detail'
  },
  '领料出库': {
    dirname: 'stock/Materiel',
    name: 'Materiel'
  },
  '领料统计': {
    dirname: 'stock/Materiel/Statistics',
    name: 'Statistics'
  },
  '领料单据': {
    dirname: 'stock/Materiel/BillList',
    name: 'BillList'
  },
  '领料明细': {
    dirname: 'stock/Materiel/Detail',
    name: 'Detail'
  },
  '报损管理': {
    dirname: 'stock/Wastage',
    name: 'Wastage'
  },
  '报损统计': {
    dirname: 'stock/Wastage/Statistics',
    name: 'Statistics'
  },
  '报损单': {
    dirname: 'stock/Wastage/Bill',
    name: 'Bill'
  },
  '报损单据': {
    dirname: 'stock/Wastage/WastageList',
    name: 'WastageList'
  },
  '报损明细': {
    dirname: 'stock/Wastage/WastageDetail',
    name: 'WastageDetail'
  },
  '库存盘点': {
    dirname: 'stock/Inventory',
    name: 'Inventory'
  },
  '库存统计': {
    dirname: 'stock/Inventory/Statistics',
    name: 'Statistics'
  },
  '盘点单': {
    dirname: 'stock/Inventory/Bill',
    name: 'Bill'
  },
  '盘点单据': {
    dirname: 'stock/Inventory/BillList',
    name: 'BillList'
  },
  '盘点明细': {
    dirname: 'stock/Inventory/Detail',
    name: 'Detail'
  },

  //收银
  '快捷开单': {
    dirname: 'cashier/QuickOrder',
    name: 'QuickOrder'
  },
  '开单': {
    dirname: 'cashier/QuickOrder/Order',
    name: 'Order'
  },
  '待结算单据': {
    dirname: 'cashier/QuickOrder/UncompletedOrder',
    name: 'UncompletedOrder'
  },
  '已结算单据': {
    dirname: 'cashier/QuickOrder/CompletedOrder',
    name: 'CompletedOrder'
  },
  '客户资料': {
    dirname: 'cashier/CustomerInfo',
    name: 'CustomerInfo'
  },
  '停用列表': {
    dirname: 'cashier/CustomerInfo/CustomerStopped',
    name: 'CustomerStopped'
  },
  '消费记录': {
    dirname: 'cashier/CustomerInfo/Consume',
    name: 'Consume'
  },

  // 财务
  '采购应付': {
    dirname: 'finance/Cost',
    name: 'Cost'
  },
  '营业汇总': {
    dirname: 'finance/Summary',
    name: 'Summary'
  },
  '汇总':{
    dirname: 'finance/Summary/BusinessSummary',
    name: 'BusinessSummary'
  },
  '施工统计': {
    dirname: 'finance/Summary/TurnoverStatistics',
    name: 'TurnoverStatistics'
  },
  '采购统计汇总': {
    dirname: 'finance/Summary/PurchaseAmount',
    name: 'PurchaseAmount'
  },
  '总到店台次': {
    dirname: 'finance/Summary/PassengerFlow',
    name: 'PassengerFlow'
  }

}

for (const name in mapInfo) {
  const item = mapInfo[name]
  const component = require('./page/' + item.dirname + '/' + item.name)['default']

  item.component = component

  module.exports[name] = item
}
