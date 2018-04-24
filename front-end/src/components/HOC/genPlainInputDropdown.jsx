import React, { Component } from 'react'
import Input from './form/Input'

function genPlainInputDropdown({ PlainDropdown, name }) {

  return class PlainInputDropdown extends Component {
    constructor(props) {
      super(props)
    }

    render() {
      return (
        <Input DplComponent={ PlainDropdown } text={ name } { ...this.props } />
      )
    }
  }
}

export default genPlainInputDropdown
