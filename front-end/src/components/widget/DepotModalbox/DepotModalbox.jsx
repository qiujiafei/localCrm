/*
 * 选择仓库组件
 * Author: tanglijun
 * Date: 2018-02-13
 */

import genSearchModalbox from '../../HOC/genSearchModalbox'
import columns from './columns'
import apiInfo from './apiInfo'

export default genSearchModalbox({
  title: '选择仓库',
  width: '800px',
  height: 'auto',
  columns: columns.slice(),
  apiInfo
})
