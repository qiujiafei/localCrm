import React, { Component } from 'react'
import PropTypes from 'prop-types'
import _ from 'lodash'

class Radio extends Component {
  static propTypes = {
    name: PropTypes.string,
    onChange: PropTypes.func,
    checked: PropTypes.bool
  }

  static defaultProps = {
    onChange: () => {}
  }

  constructor(props) {
    super(props)
  }

  handleChange(e) {
    const radio = e.currentTarget.querySelector('input[type="radio"]')
    const radioGroup = document.querySelectorAll('input[name=' + radio.name + ']')

    _.each(radioGroup, radioItem => {
      radioItem.parentNode.className = 'crm-radio unselected'
      radioItem.checked = false
    })

    e.currentTarget.className = 'crm-radio selected'
    radio.checked = true

    this.props.onChange(e)
  }

  render() {
    const { name, checked, ...rest } = this.props

    return (
      <div
        className={ !checked ? "crm-radio unselected" : 'crm-radio selected' }
        onClick={ e => this.handleChange(e) }
        ref={ wrapper => this.wrapper = wrapper }
      >
        <input
          type="radio"
          name={ name || 'radioBtn' }
          { ...rest }
        />
      </div>
    )
  }
}

export default Radio
