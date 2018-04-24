import React, { Component } from 'react'
import Input from '../../HOC/form/Input'
import SupplyDropdown from './dropdown/Supply'

class InputSupply extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <Input DplComponent={ SupplyDropdown } text="供应商" { ...this.props } />
    )
  }
}

export default InputSupply
