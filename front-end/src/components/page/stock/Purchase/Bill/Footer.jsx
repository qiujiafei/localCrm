/* global _ */

import React, { Component } from 'react'
import PropTypes from 'prop-types'
import Button from '../../../../widget/Button'
import ajax from '../../../../../lib/ajax'

class Footer extends Component {
  static propTypes = {
    pageInstance: PropTypes.object
  }

  constructor(props) {
    super(props)
  }

  save(data) {
    const { pageInstance } = this.props

    pageInstance.showIndicator()

    ajax({
      method: 'POST',
      url: 'purchase/put/insert.do',
      data: data
    }).then(info => {
      if (info.err) {
        this.props.pageInstance.showToolTip(info.desc, 'failed')

        if (info.goToLogin) {
          location.href = '/login'
        }
      } else {
        pageInstance.showToolTip(data.status === 1 ? '入库成功' : '挂单成功', 'success')
        setTimeout(() => { document.querySelector('[data-id="采购明细"]').click() }, 3000)
      }

      this.props.pageInstance.hideIndicator()
    })
  }

  onBtnClick(status) {
    const { pageInstance } = this.props
    const headerElements = pageInstance.billPage.querySelectorAll('header [data-api-id]')
    const calcElements = pageInstance.billPage.querySelectorAll('.calculation [data-api-id]')
    const data = {}
    let pass = true

    // 头部数据
    headerElements.forEach(headerElement => {
      const id = headerElement.getAttribute('data-api-id')
      const keyValue = headerElement.getAttribute('data-key')

      data[id] = keyValue ? keyValue : headerElement.value
    })

    // 计算金额区域数据
    calcElements.forEach(calcElement => {
      const id = calcElement.getAttribute('data-api-id')

      data[id] = calcElement.value
    })

    data.commodities = pageInstance.apiData.map(item => {
      return {
        commodity_id: item.commodity_id,
        depot_id: item.depot_id,
        quantity: item.quantity,
        unit_id: item.unit_id,
        current_price: item.single_price,
        last_purchase_price: item.last_purchase_price,
        comment: item.comment
      }
    })

    if (status === 1) {
      data.status = status
    }

    // 本次付款 < 0 不能入库
    if (Number(data.settlement_price) < 0) {
      pageInstance.showToolTip('本次付款不能为负数', 'failed')
      pass = false
    }

    // 验证必填项
    const outer = {
      supplier_id: '请选择供应商'
    }

    _.each(outer, (value, key) => {
      if (!data[key]) {
        this.props.pageInstance.showToolTip(value, 'failed')
        pass = false
        return false
      }
    })

    // 过滤 commodities 中的空数据
    const newCommodities = []

    _.each(data.commodities, (commodity) => {
      const total = Object.keys(commodity).length
      let emptyLen = 0

      _.each(commodity, item => {
        if (!item) {
          emptyLen += 1
        }
      })

      if (total !== emptyLen) {
        newCommodities.push(commodity)
      }
    })

    data.commodities = newCommodities

    if (pass) {
      this.save(data)
    }
  }

  render() {
    return (
      <div>
        {/* <Button type="secondary" text="挂单" onClick={ () => { this.onBtnClick() } } /> */}
        <Button type="secondary" text="入库" onClick={ () => this.onBtnClick(1) } />
      </div>
    )
  }
}

export default Footer
