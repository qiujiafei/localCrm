import React, { Component } from 'react'
import Dropdown from '../HOC/form/Dropdown'

class DropdownProvince extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <Dropdown
        api="/carbasicinformation/get/getCarNumberProvince.do"
        dataKey="data"
        dataId="id"
        dataName="name"
        placeholder="选择省"
        noPage={ true }
        { ...this.props }
      />
    )
  }
}

export default DropdownProvince
