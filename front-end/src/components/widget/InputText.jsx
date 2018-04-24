import React, { Component } from 'react'
import Input from './BasicInput'

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
