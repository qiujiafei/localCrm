/*
 * 选择计量但组件
 * Author: tanglijun
 * Date: 2018-03-06
 */

import genSearchModalbox from '../../HOC/genSearchModalbox'
import columns from './columns'
import apiInfo from './apiInfo'

export default genSearchModalbox({
  title: '选择计量单位',
  width: '800px',
  height: 'auto',
  columns: columns.slice(),
  apiInfo
})
