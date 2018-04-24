import genBasicPage from '../../../../HOC/genBasicPage'
import columns from './columns'
import apiInfo from './apiInfo'
import menus from './menus'
import Modalbox from './Modalbox'
import Search from '../../../achievement/Employee/Fighter/Search'

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
  ]
})
