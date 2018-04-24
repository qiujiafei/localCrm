import genImportPage from '../../../HOC/genImportPage'

export default genImportPage({
  title: '导入库存商品步骤以及注意事项',
  list: [
    '先下载模版，按模版来填写。',
    '条形码、商品名称、销售价格、单位、所属分类是必填项。条形码可以自己定义。且所属分类必须与系统中保存的名称一致。',
    '同样不能点击两次确定，会导致库存数量金额翻倍。',
    '如有出现不符合的数据，会提示错误表格并导出表格，用批注提示错误原因，进行修改保存后，再进行导入即可。'
  ],
  url: '/import/data/commodity.do',
  templateUrl: '/export/data/commodity.do'
})
