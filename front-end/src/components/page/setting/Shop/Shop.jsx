import genBasicPage from '../../../HOC/genBasicPage'
import columns from './columns'
import apiInfo from './apiInfo'
import menus from './menus'
import Modalbox from './Modalbox'
import UnControlledModalbox from './UnControlledModalbox'

export default genBasicPage({
  columns,
  apiInfo,
  menus,
  Modalbox,
  UnControlledModalbox,
  lineBtns: [
    {
      id: 'editLine',
      name: '查看',
      className: 'link'
    }
  ]
})
