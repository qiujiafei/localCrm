import genImportPage from '../../../HOC/genImportPage'

export default genImportPage({
  title: '导入服务项目信息步骤以及注意事项',
  list: [
    '先下载模版，按模版来填写。',
    '服务项目、编码、所属分类都是必填项，且所属分类必须与系统中保存的名称一致。',
    '同样不能点击两次确定，会导致项目重复。'
  ],
  url: '/import/data/service.do',
  templateUrl: '/export/data/service.do'
})
