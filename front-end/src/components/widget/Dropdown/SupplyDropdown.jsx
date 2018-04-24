import React, { Component } from 'react'
import PropTypes from 'prop-types'
import ajax from '../../../lib/ajax'

import CommonDropdown from './CommonDropdown'

class SupplyDropdown extends Component {
  static propTypes = {
    pageInstance: PropTypes.object
  }

  constructor(props) {
    super(props)

    this.state = {
      data: []
    }
  }

  getData() {
    ajax({
      method: 'GET',
      url: '/supplier/get/lists.do',
      data: {
        count_per_page: 999,
        page_num: 1
      }
    })
      .then(info => {
        if (info.err) {
          if (info.goToLogin) {
            location.href = '/login'
          }
        } else {
          this.setState({ data: this.formatData(info.data.lists) })
        }
      })
  }

  onArrowClick() {
    this.getData()
  }

  formatData(data) {
    const result = []

    for (let i = 0; i < data.length; i += 1) {
      const item = data[i]

      result.push({
        id: item.id,
        name: item.main_name
      })
    }

    return result
  }

  render() {
    return (
      <CommonDropdown data={ this.state.data } { ...this.props } onArrowClick={ () => this.onArrowClick() } />
    )
  }
}

export default SupplyDropdown
