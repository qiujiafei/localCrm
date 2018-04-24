/*
 * 选择客户组件
 * Author: tanglijun
 * Date: 2018-02-20
 */

import genSearchModalbox from '../../../../../HOC/genSearchModalbox'
import columns from './columns'
import apiInfo from './apiInfo'

export default genSearchModalbox({
  title: '选择客户',
  width: '1000px',
  height: 'auto',
  columns: columns.slice(),
  apiInfo
})
