import React, { Component } from 'react'
import Checkbox from './Checkbox'

class CheckboxTest extends Component {
  constructor(props) {
    super(props)
  }
  render() {
    return (
      <div>
        <div>
          <h1>多选框未选中状态</h1>
          <Checkbox />
        </div>
        <div>
          <h1>多选框选中状态</h1>
          <Checkbox checked />
        </div>
      </div>
    )
  }
}

export default CheckboxTest
