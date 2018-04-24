import React, { Component } from 'react'
import Input from '../../HOC/form/Input'

class InputText extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <Input { ...this.props } />
    )
  }
}

export default InputText
