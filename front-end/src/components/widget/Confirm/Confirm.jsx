import React, { Component } from 'react'
import PropTypes from 'prop-types'
import Button from '../Button'

import './Confirm.styl'

class Confirm extends Component {
  static propTypes = {
    type: PropTypes.string,
    show: PropTypes.bool,
    msg: PropTypes.string,
    onCancelClick: PropTypes.func,
    onSubmitClick: PropTypes.func
  }

  constructor(props) {
    super(props)

    this.state = {
      show: false,
      msg: props.msg,
      type: props.type
    }
  }

  componentWillReceiveProps(nextProps) {
    this.setState(nextProps)
  }

  handleCancelClick() {
    if (typeof this.props.onCancelClick === 'function') {
      this.props.onCancelClick()
    }
  }

  handleSubmitClick() {
    if (typeof this.props.onSubmitClick === 'function') {
      this.props.onSubmitClick(this.state.type)
    }
  }

  render() {
    return this.state.show ? (
      <div className="crm-confirm-wrapper">
        <div className="inner">
          <div className="box">
            <div className="msg-box">
              <div className="inner">
                <div className="msg">{ this.state.msg }</div>
              </div>
            </div>
            <footer>
              <Button text="是" size="small" type="primary" onClick={ () => this.handleSubmitClick() } />
              <Button text="否" size="small" onClick={ () => this.handleCancelClick() } />
            </footer>
          </div>
        </div>
      </div>
    ) : null
  }
}

export default Confirm
