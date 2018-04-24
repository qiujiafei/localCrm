import React, { Component } from 'react'
import Input from '../HOC/form/Input'
import WorkerPlainDropdown from './WorkerPlainDropdown'

class InputWorker extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <Input DplComponent={ WorkerPlainDropdown } text="施工人员" { ...this.props } />
    )
  }
}

export default InputWorker
