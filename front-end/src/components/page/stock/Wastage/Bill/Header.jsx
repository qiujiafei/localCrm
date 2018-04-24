import React, { Component } from 'react'
import InputDateTime from '../../../../widget/form/InputDateTime'

class Header extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <div>
        <InputDateTime text="报损时间" readOnly disabled />
      </div>
    )
  }
}

export default Header
