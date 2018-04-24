import React, { Component } from 'react'
import Input from './form/Input'
import CommonDropdown from '../widget/Dropdown/CommonDropdown'

function genInputDropdown() {
  return class InputDropdown extends Component {
    render() {
      return (
        <Input DplComponent={ CommonDropdown } { ...this.props } />
      )
    }
  }
}

export default genInputDropdown
