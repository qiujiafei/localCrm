import genBasicPage from '../../../../HOC/genBasicPage'
import columns from './columns'
import apiInfo from './apiInfo'
import menus from './menus'
import Modalbox from './Modalbox'
import Search from './Search'

function onModalboxSubmitClick(data, pageInstance) {
  if (/^[a-zA-Z]+$/.test(data.taxpayer_identification_number)) {
    pageInstance.showTip('纳税人识别格式错误', 'failed')
    return false
  }

  if (!/^[1][3,4,5,7,8][0-9]{9}$/.test(data.cell_number)) {
    pageInstance.showTip('手机格式错误', 'failed')
    return false
  }

  if (data.bank_card_number) {
    if (!/^(\d{16}|\d{19})+$/.test(data.bank_card_number)) {
      pageInstance.showTip('银行卡格式错误长度16位或19位', 'failed')
      return false
    }
  }

  return data
}

export default genBasicPage({
  columns,
  apiInfo,
  menus,
  Modalbox,
  Search,
  onModalboxSubmitClick
})
