import React, { Component } from 'react'
import PropTypes from 'prop-types'
import ajax from '../../../lib/ajax'

import CommonDropdown from './CommonDropdown'

class UnitDropdown extends Component {
  static propTypes = {
    pageInstance: PropTypes.object
  }

  constructor(props) {
    super(props)

    const { pageInstance, ...rest } = props

    this.state = {
      data: [],
      rest,
      pageInstance
    }
  }

  componentDidMount() {
    this.getData()
  }

  getData() {
    ajax({
      method: 'GET',
      url: '/employee/get/getall.do',
      data: {
        count_per_page: 999,
        page_num: 1
      }
    })
      .then(info => {
        if (info.err) {
          if (info.goToLogin) {
            this.state.pageInstance.showTip('登录超时', 'failed')
            setTimeout(() => {
              location.href = '/login'
            }, 2500)
          }
        } else {
          this.setState({ data: this.formatData(info.data.employee) })
        }
      })
  }

  formatData(data) {
    const result = []

    for (let i = 0; i < data.length; i += 1) {
      const item = data[i]

      result.push({
        id: item.id,
        name: item.name
      })
    }

    return result
  }

  render() {
    return (
      <CommonDropdown data={ this.state.data } { ...this.state.rest } />
    )
  }
}

export default UnitDropdown
