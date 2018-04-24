/*
 * 选择商品组件
 * Author: tanglijun
 * Date: 2018-02-13
 */

import genAdvancedModalbox from '../../../../../HOC/genAdvancedModalbox'
import columns from './columns'
import apiInfo from './apiInfo'

export default genAdvancedModalbox({
  title: '选择商品',
  width: '1000px',
  height: 'auto',
  columns: columns.slice(),
  apiInfo
})
