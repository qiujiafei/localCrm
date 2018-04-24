import React, { Component } from 'react'
import Dropdown from '../../../HOC/form/Dropdown'

class Supply extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <Dropdown api="/supplier/get/lists.do" dataKey="lists" dataId="id" dataName="main_name" placeholder="--请选择供应商--" { ...this.props } />
    )
  }
}

export default Supply
