import React, { Component } from 'react'
import Input from '../HOC/form/Input'
import DepotPlainDropdown from './DepotPlainDropdown'

class InputWorker extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <Input DplComponent={ DepotPlainDropdown } text="仓库" { ...this.props } />
    )
  }
}

export default InputWorker
