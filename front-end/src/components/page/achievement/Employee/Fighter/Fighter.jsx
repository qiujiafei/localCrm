import genBasicPage from '../../../../HOC/genBasicPage'
import columns from './columns'
import apiInfo from './apiInfo'
import menus from './menus'
import Modalbox from './Modalbox'
import Search from './Search'

function onModalboxSubmitClick(data, pageInstance) {
  if (data.ID_code) {
    if (!/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/.test(data.ID_code)) {
      pageInstance.showTip('身份证格式错误', 'failed')
      return false
    }
  }

  if (!/^[1][3,4,5,7,8][0-9]{9}$/.test(data.phone_number)) {
    pageInstance.showTip('手机格式错误', 'failed')
    return false
  }

  return data
}

export default genBasicPage({
  columns,
  apiInfo,
  menus,
  Modalbox,
  Search,
  lineBtns: [
    {
      id: 'editLine',
      name: '修改',
      className: 'link'
    }
  ],
  onModalboxSubmitClick
})
