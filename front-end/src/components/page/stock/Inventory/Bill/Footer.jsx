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
      url: '/inventory/put/insert.do',
      data: data
    }).then(info => {
      if (info.err) {
        pageInstance.showToolTip(info.desc, 'failed')

        if (info.goToLogin) {
          location.href = '/login'
        }
      } else {
        pageInstance.showToolTip('盘点成功', 'success')
        setTimeout(() => { document.querySelector('[data-id="盘点单据"]').click() }, 3000)
      }

      pageInstance.hideIndicator()
    })
  }

  onSubmit() {
    const { pageInstance } = this.props
    const data = {}
    const commodities = []

    data.depot_id = pageInstance.billPage.querySelector('[data-api-id="depot_id"').value

    pageInstance.originData.forEach((item, index) => {
      commodities.push({
        commodity_id: item.commodity_id,
        depot_id: item.depot_id,
        unit_id: item.unit_id,
        quantity: pageInstance.apiData[index].quantity,
        inventory_quantity: item.stock,
        commodity_batch_id: item.commodity_batch_id
      })
    })

    data.commodities = commodities

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
