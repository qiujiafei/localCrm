/* global WdatePicker moment */

import React, { Component } from 'react'
import PropTypes from 'prop-types'
import Input from '../../HOC/form/Input'

class InputDateTime extends Component {
  static propTypes = {
    dateOption: PropTypes.object
  }

  constructor(props) {
    super(props)

    const { dateOption, ...rest } = this.props

    this.dateOption = dateOption
    this.rest = rest
  }

  run() {
    this.timer = setInterval(() => {
      this.ipt.value = moment().format('YYYY-MM-DD HH:mm:ss')
    }, 100)
  }

  componentDidMount() {
    if (this.props.hasOwnProperty('disabled')) {
      this.ipt.value = moment().format('YYYY-MM-DD HH:mm:ss')
      this.run()
    } else {
      this.ipt.onclick = e => {
        WdatePicker(Object.assign({
          el: e.currentTarget,
          lang: 'zh-cn',
          dateFmt: 'yyyy-MM-dd HH:mm:ss',
          position: {
            top: 7,
            left: -6
          }
        }, this.dateOption))
      }
    }
  }

  componentWillUnmount() {
    this.ipt.onclick = null
    clearInterval(this.timer)
  }

  render() {
    return (
      <Input parentInstance={ this } { ...this.rest } />
    )
  }
}

export default InputDateTime
