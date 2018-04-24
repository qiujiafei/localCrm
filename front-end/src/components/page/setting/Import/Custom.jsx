import genImportPage from '../../../HOC/genImportPage'

export default genImportPage({
  title: '导入客户资料步骤以及注意事项：',
  list: [
    '先下载模版，按模版来填写。',
    '车牌号、发动机号、车架号、所属分店是必填项。其他的资料可根据自己的需求填写。',
    '会员客户填写卡号，如果是散客就无需填写。',
    '在导入表格前，我们需要核对数据是否有重复数据，以免数据重复。',
    '导入的会员数据不要点击两次确定，会导致会员的金额翻倍。',
    '如有出现不符合的数据，会提示错误表格并导出表格，用批注提示错误原因，进行修改保存后，再进行导入即可。'
  ],
  url: '/import/data/customer.do',
  templateUrl: '/export/data/customer.do'
})
