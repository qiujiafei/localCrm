import React, { Component } from 'react'
import PropTypes from 'prop-types'

import './Tooltip.styl'

class Tooltip extends Component {
  static propTypes = {
    text: PropTypes.string.isRequired,
    type: PropTypes.string.isRequired,
    show: PropTypes.bool.isRequired
  }

  static defaultProps = {
    text: '成功',
    type: 'normal',
    show: false
  }

  constructor(props) {
    super(props)

    this.state = {
      text: props.text,
      type: props.type,
      show: props.show
    }
  }

  componentWillReceiveProps(nextProps) {
    this.setState(nextProps)
  }

  render() {
    return this.state.show ? (
      <div className="crm-tooltip-wrapper">
        <div className="crm-tooltip-inner">
          <div className={ 'crm-tooltip-box crm-tooltip-' + this.state.type }>
            <div className="crm-tooltip-box-inner">
              <i></i>
              <span>{ this.state.text }</span>
            </div>
          </div>
        </div>
      </div>
    ) : null
  }
}

export default Tooltip
