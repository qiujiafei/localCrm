/* global _ */

import React, { Component } from 'react'
import Button from '../../../widget/Button'
import Indicator from '../../../widget/Indicator/Indicator'
import Tooltip from '../../../widget/Tooltip/Tooltip'
import CustomerModalbox from './CustomerModalbox/CustomerModalbox'
import ajax from '../../../../lib/ajax'
import util from '../../../../lib/util'
import './Card.styl'

class MemberCard extends Component {
  constructor(props) {
    super(props)

    this.state = {

      // 模态框
      customerModalboxDisplay: false,

      // 提示框
      tooltipDisplay: false,
      tooltipText: '',
      tooltipType: '',

      // 加载动画
      indicatorDisplay: false
    }
  }

  handleClick() {
    const data = this.getData()

    if (this.validate(data)) {
      this.showIndicator()

      ajax({
        method: 'POST',
        url: '/customerinfomation/modify/ismember.do',
        data: this.getData()
      }).then(info => {
        if (info.err) {
          this.showTip(info.desc, 'failed')

          if (info.goToLogin) {
            setTimeout(() => {
              location.href = '/login'
            }, 3000)
          }
        } else {
          this.showTip('办卡成功！', 'success')
          this.clear()
        }

        this.hideIndicator()
      })
    }
  }

  handleCustomerClick() {
    this.showCustomerModalbox()
  }

  showTip(text, type) {
    this.setState({ tooltipDisplay: true, tooltipText: text, tooltipType: type })

    setTimeout(() => { this.hideTip() }, 3000)
  }

  hideTip() {
    this.setState({ tooltipDisplay: false })
  }

  showCustomerModalbox() {
    this.setState({ customerModalboxDisplay: true })
  }

  hideCustomerModalbox() {
    this.setState({ customerModalboxDisplay: false })
  }

  showIndicator() {
    this.setState({ indicatorDisplay: true })
  }

  hideIndicator() {
    this.setState({ indicatorDisplay: false })
  }

  getData() {
    const elements = this.wrapper.querySelectorAll('[data-api-id]')
    const result = {}

    elements.forEach(element => {
      const id = element.getAttribute('data-api-id')

      result[id] = element.value
    })

    return result
  }

  validate(data) {
    let result = true

     _.each(data, (item, key) => {
      const element = this.wrapper.querySelector('[data-api-id="' + key + '"]')

      if (element.hasAttribute('required')) {
        if (!element.value) {
          this.showTip(element.getAttribute('data-err-msg'), 'failed')
          result = false
          return false
        }
      }
    })

    return result
  }

  clear() {
    const elements = this.wrapper.querySelectorAll('[data-api-id]')

    _.each(elements, element => {
      if (element.getAttribute('data-api-id') !== 'type') {
        element.value = ''
      }
    })
  }

  transferTrData(data) {
    const { row, original } = data.rowInfo

    _.each(row, (item, key) => {
      const element = this.wrapper.querySelector('[data-api-id="' + key + '"]')

      if (element) {
        if (key === 'number') {
          element.value = original.number_plate_province_name + original.number_plate_alphabet_name + original.number_plate_number
        } else {
          element.value = item
        }
      }
    })

    this.hideCustomerModalbox()
  }

  handleClose() {
    this.hideCustomerModalbox()
  }

  render() {
    return (
      <div className="crm-membercard" ref={ wrapper => this.wrapper = wrapper }>

        { this.state.customerModalboxDisplay && <CustomerModalbox parentPage={ this } transferTrData={ data => this.transferTrData(data) } handleClose={ () => this.handleClose() } /> }

        <Tooltip
          text={ this.state.tooltipText }
          type={ this.state.tooltipType }
          show={ this.state.tooltipDisplay }
        />

        <Indicator
          show={ this.state.indicatorDisplay }
        />

        <div className="inner">
          <div className="box">
            <div className="left">
              <header>基础资料<Button text="选择客户" type="link" onClick={ e => this.handleCustomerClick(e) } /></header>
              <input style={ { display: 'none' } } data-api-id="id" data-err-msg="请选择客户" type="text" readOnly required />
              <table>
                <tbody>
                  <tr>
                    <td>姓名</td>
                    <td>
                      <div>
                        <input data-api-id="customer_name" type="text" readOnly="true" />
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>车牌</td>
                    <td>
                      <div>
                        <input data-api-id="number" type="text" readOnly="true" />
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>手机</td>
                    <td>
                      <div>
                        <input data-api-id="cellphone_number" type="text" readOnly="true"/>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>车型</td>
                    <td>
                      <div>
                        <input data-api-id="brand_name" type="text" readOnly="true"/>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div className="right">
              <header>办卡信息</header>
              <table>
                <tbody>
                  <tr>
                    <td><span className="crm-color crm-color-error">*</span>卡号</td>
                    <td>
                      <div>
                        <input
                          onInput={ e => e.currentTarget.value = util.formateToInteger(e.currentTarget.value) }
                          data-api-id="card_number"
                          data-err-msg="请输入卡号"
                          placeholder="请输入卡号"
                          maxLength="30"
                          type="text"
                          required
                        />
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td><span className="crm-color crm-color-error">*</span>办卡金额</td>
                    <td>
                      <div>
                        <input
                          onInput={ e => e.currentTarget.value = util.formateToDecimal(e.currentTarget.value) }
                          onBlur={ e => e.currentTarget.value = Number(e.currentTarget.value).toFixed(2) }
                          type="text"
                          data-api-id="price"
                          data-err-msg="请输入金额"
                          placeholder="请输入金额"
                          maxLength="30"
                          required
                        />
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td><span className="crm-color crm-color-error">*</span>会员卡种</td>
                    <td>
                      <div>
                        <input type="text" data-api-id="type" data-err-msg="请输入卡种" placeholder="请输入卡种" defaultValue="打折卡" readOnly required />
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>备注</td>
                    <td>
                      <textarea data-api-id="customer_comment" data-err-msg="请输入备注" placeholder="请输入备注" maxLength="200" ></textarea>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <footer>
            <Button text='确定' type='primary' onClick={ e => this.handleClick(e) } />
          </footer>
        </div>
      </div>
    )
  }
}

export default MemberCard
