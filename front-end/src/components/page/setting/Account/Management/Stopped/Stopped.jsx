import genBasicPage from '../../../../../HOC/genBasicPage'
import columns from './columns'
import apiInfo from './apiInfo'
import menus from './menus'
import Search from '../../../../achievement/Employee/Fighter/Search'

export default genBasicPage({
  columns,
  apiInfo,
  menus,
  Search,
  lineBtns: [
    {
      id: 'delLine',
      name: '删除',
      className: 'link-gray'
    }
  ]
})
