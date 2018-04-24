import React, { Component } from 'react'
import Dropdown from '../../../HOC/form/Dropdown'

class UnitDropdown extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <Dropdown api="/commodityUnit/get/getall.do" dataKey="unit" dataId="id" dataName="unit_name" placeholder="--请选择单位--" { ...this.props } />
    )
  }
}

export default UnitDropdown
