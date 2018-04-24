import React, { Component } from 'react';
import PropTypes from 'prop-types'
import Button from '../Button'

import './Alert.styl'

class Alert extends Component {
  static propTypes = {
    show: PropTypes.bool,
    msg: PropTypes.string,
    content: PropTypes.string,
    onSubmitClick: PropTypes.func
  }

  constructor(props) {
    super(props)

    this.state = {
      show: false,
      msg: props.msg,
      content: props.content
    }
  }

  componentWillReceiveProps(nextProps) {
    this.setState(nextProps)
  }

  handleSubmitClick() {
    this.show()

    if (typeof this.props.onSubmitClick === 'function') {
      this.props.onSubmitClick(this)
    }
  }

  show() {
    this.setState({ show: true })
  }

  hide() {
    this.setState({ show: false })
  }

  render() {
    const Component = this.state.show ? (
      <div className="crm-alter-wrapper">
        <div className="inner">
          <div className="box">
            <div className="msg-box">
              <div className="inner">
                <div className="msg">{ this.state.msg }</div>
                <div className="content">{ this.state.content }</div>
              </div>
            </div>
            <footer>
              <Button text="确定" size="middle" type="primary" onClick={ () => this.handleSubmitClick() }></Button>
            </footer>
          </div>
        </div>
      </div>
    ) : null

    return Component
  }
}

export default Alert
