import React, { Component } from 'react'
import PropTypes from 'prop-types'
import InputText from '../../../../widget/form/InputText'
import util from '../../../../../lib/util'

class Calculation extends Component {
  static propTypes = {
    pageInstance: PropTypes.object
  }

  constructor(props) {
    super(props)
  }

  handleDiscountInput(e) {
    const totalPrice = this.wrapper.querySelector('[data-api-id="origin_price"]').value
    const targetElement = this.wrapper.querySelector('[data-api-id="settlement_price"]')

    e.currentTarget.value = util.formateToDecimal(e.currentTarget.value)

    // 计算本次付款
    if (totalPrice > 0) {
      targetElement.value = ((Number(totalPrice) || 0) - Number(e.currentTarget.value)).toFixed(2)
    }
  }

  render() {
    return (
      <div ref={ wrapper => this.wrapper = wrapper }>
        <InputText
          data-api-id="origin_price"
          text="应付金额"
          disabled
        />
        <InputText
          data-api-id="discount"
          text="优惠金额"
          onInput={ e => this.handleDiscountInput(e) }
          onBlur={
            e => {
              if (e.currentTarget.value) {
                e.currentTarget.value = Number(e.currentTarget.value).toFixed(2)
              }
            }
          }
        />
        <InputText
          data-api-id="settlement_price"
          text="本次付款"
          disabled
        />
        <InputText
          data-api-id="comment"
          text="备注"
        />
      </div>
    )
  }
}

export default Calculation
