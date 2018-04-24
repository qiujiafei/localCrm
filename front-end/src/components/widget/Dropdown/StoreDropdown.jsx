import React, { Component } from 'react'
import ajax from '../../../lib/ajax'

import CommonDropdown from './CommonDropdown'

class StoreDropdown extends Component {
  constructor(props) {
    super(props)

    this.state = {
      data: []
    }
  }

  componentDidMount() {
    this.getData()
  }

  getData() {
    ajax({
      method: 'GET',
      url: '/depot/get/lists.do',
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
          this.setState({
            data: this.formatData(info.data.lists)
          })
        }
      })
  }

  formatData(data) {
    const result = []

    for (let i = 0; i < data.length; i += 1) {
      const item = data[i]

      result.push({
        id: item.depot_name,
        name: item.depot_name
      })
    }

    return result
  }

  render() {
    return (
      <CommonDropdown data={ this.state.data } { ...this.props } />
    )
  }
}

export default StoreDropdown
