import React, { Component } from 'react'
import Dropdown from '../../../HOC/form/Dropdown'

class EmployeeTypeDropdown extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <Dropdown api="/employeetype/get/getall.do" dataKey="employeetype" dataId="id" dataName="name" { ...this.props } />
    )
  }
}

export default EmployeeTypeDropdown
