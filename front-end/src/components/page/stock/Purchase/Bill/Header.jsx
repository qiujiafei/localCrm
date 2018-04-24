import React, { Component } from 'react'
import InputDateTime from '../../../../widget/form/InputDateTime'
import InputSupply from '../../../../widget/form/InputSupply'

class Header extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <div>
        <InputSupply data-api-id="supplier_id" { ...this.props } required />
        <InputDateTime text="采购时间" readOnly disabled />
      </div>
    )
  }
}

export default Header
