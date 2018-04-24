import React, { Component } from 'react'
import Dropdown from '../../../HOC/form/Dropdown'

class Depot extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <Dropdown
        api="/depot/get/lists.do"
        dataKey="lists"
        dataId="id"
        dataName="depot_name"
        placeholder="--请选择仓库--" { ...this.props }
      />
    )
  }
}

export default Depot
