import React, { Component } from 'react'
import InputDateTime from './InputDateTime'

class InputDateTimeGroup extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <div className="crm-datetime-group">
        <InputDateTime icon="date" text="采购时间" placeholder="请输入起始时间" readOnly noLabel />
        <InputDateTime icon="date" text="至" placeholder="请输入结束时间" readOnly />
      </div>
    )
  }
}

export default InputDateTimeGroup
