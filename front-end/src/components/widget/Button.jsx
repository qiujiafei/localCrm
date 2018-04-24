import React, { Component } from 'react'
import PropTypes from 'prop-types'

class Button extends Component {
  static propTypes = {
    text: PropTypes.string,
    size: PropTypes.oneOf([
      'large',
      'middle',
      'small'
    ]),
    type: PropTypes.oneOf([
      'normal',
      'primary',
      'secondary',
      'warning',
      'error',
      'assist',
      'link',
      'link-gray'
    ]),
    onClick: PropTypes.func
  }

  static defaultProps = {
    text: '按钮',
    size: 'large',
    type: 'normal',
    onClick: () => {}
  }

  constructor(props) {
    super(props)
  }

  render() {
    const className = 'crm-button button-' + this.props.size + ' button-' + this.props.type
    const { text, ...rest } = this.props
    return (
      <button
        className={ className }
        role="button"
        onClick={ e => { this.props.onClick(e) } }
        title={ text }
        { ...rest }
      >
        { text }
      </button>
    )
  }
}

export default Button
