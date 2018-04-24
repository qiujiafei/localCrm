import React, { Component } from 'react'
import PropTypes from 'prop-types'

import './Checkbox.styl'

class Checkbox extends Component {
  static propTypes = {
    id: PropTypes.string,
    defaultChecked: PropTypes.bool,
    onChange: PropTypes.func,
    disabled: PropTypes.bool
  }

  static defaultProps = {
    onChange: () => {}
  }

  constructor(props) {
    super(props)
    this.state = { checked: props.defaultChecked || false }
  }

  handleChange(e) {
    const checkbox = e.currentTarget
    const checkboxParentDiv = checkbox.parentNode

    if (checkbox.checked) {
      checkboxParentDiv.className = 'checkbox checkbox-selected'
    } else {
      checkboxParentDiv.className = 'checkbox checkbox-unselected'
    }

    this.setState({ checked: checkbox.checked })
  }

  render () {
    const { onChange, id, ...rest } = this.props

    let checkboxClass = [ 'checkbox' ]

    if (this.props.disabled) {
      checkboxClass.push('checkbox-disabled')
    } else {
      if (this.state.checked) {
        checkboxClass.push('checkbox-selected')
      } else {
        checkboxClass.push('checkbox-unselected')
      }
    }

    return (
      <div
        className={ checkboxClass.join(' ') }
      >
        <input
          id={ id }
          type="checkbox"
          onChange={ e => {
            this.handleChange(e)
            onChange(e)
          } }
          { ...rest }
        />
      </div>
    )
  }
}

export default Checkbox
