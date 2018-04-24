import genBasicPage from '../../../HOC/genBasicPage'
import columns from './columns'
import apiInfo from './apiInfo'
import menus from './menus'
import Modalbox from './Modalbox'
import Search from './Search'

function onModalboxSubmitClick(data) {
  if (data.type != '1') {
    data.type = '0'
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
