/* global _ */

import genBasicPage from '../../../HOC/genBasicPage'
import columns from './columns'
import apiInfo from './apiInfo'
import menus from './menus'
import Modalbox from './Modalbox'
import Search from './Search'

function onModalboxSubmitClick(data) {
  const non_car_info = [
    'id',
    'customer_name',
    'cellphone_number',
    'gender',
    'birthday',
    'ID_number',
    'address',
    'customer_origination',
    'license_image_name',
    'company',
    'is_member',
    'comment',
    'status',
    'consume_count',
    'total_consume_price'
  ].join('')

  const except = [
    'tire_brand_name'
  ].join('')

  const newData = { car_info: [ {} ] }

  for (const key in data) {
    const item = data[key]

    if (except.indexOf(key) === -1) {
      if (non_car_info.indexOf(key) !== -1) {
        newData[key] = item
      } else {
        newData['car_info'][0][key] = item
      }
    }
  }

  return newData
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
      className: 'link',
      url: '/customerinfomation/get/getone.do',
      callback: (data, pageInstance) => {
        const customerInfo = data.customer_info
        const carInfo = data.car_info[0]
        const originData = {}

        _.each(customerInfo, (value, key) => {
          originData[key] = value
        })

        _.each(carInfo, (value, key) => {
          originData[key] = value
        })

        originData.tire_brand_name = originData.brand_name

        pageInstance.originData = originData
      }
    }
  ],
  onModalboxSubmitClick
})
