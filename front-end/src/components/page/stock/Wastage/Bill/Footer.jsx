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
      url: '/damaged/put/insert.do',
      data: data
    }).then(info => {
      if (info.err) {
        pageInstance.showToolTip(info.desc, 'failed')

        if (info.goToLogin) {
          location.href = '/login'
        }
      } else {
        pageInstance.showToolTip('报损成功', 'success')
        setTimeout(() => { document.querySelector('[data-id="报损单据"]').click() }, 3000)
      }

      pageInstance.hideIndicator()
    })
  }

  onSubmit() {
    const { pageInstance } = this.props
    const calcElements = pageInstance.billPage.querySelectorAll('.calculation [data-api-id]')
    const data = {}

    // 计算金额区域数据
    calcElements.forEach(calcElement => {
      const id = calcElement.getAttribute('data-api-id')

      data[id] = calcElement.value
    })

    // 数据项
    // commodity_batch_id 为空过滤该条数据
    const commodity_gather = []

    pageInstance.apiData.forEach(item => {
      if (item.commodity_batch_id) {
        commodity_gather.push({
          commodity_batch_id: item.commodity_batch_id,
          quantity: item.quantity || '',
          comment: item.comment || ''
        })
      }
    })

    data.commodity_gather = commodity_gather

    this.save(data)
  }

  render() {
    return (
      <div>
        <Button type="secondary" text="确定" onClick={ e => this.onSubmit(e) } />
      </div>
    )
  }
}

export default Footer
