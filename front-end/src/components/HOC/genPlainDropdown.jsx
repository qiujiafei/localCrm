/*
 * 伪下拉框 HOC 组件
 * Author: tanglijun
 * Date: 2018-02-13
 */

import React, { Component } from 'react'
import PropTypes from 'prop-types'

function genPlainDropdown(option) {

  const {
    placeholder = ''
  } = option

  return class PlainDropdown extends Component {
    static propTypes = {
      handleArrowClick: PropTypes.func,
      pageInstance: PropTypes.object,
      wrapperStyle: PropTypes.object
    }

    static defaultProps = {
      handleArrowClick: () => {}
    }

    constructor(props) {
      super(props)

      const { handleArrowClick, pageInstance, wrapperStyle, ...rest } = props

      this.handleArrowClick = handleArrowClick
      this.rest = rest
      this.pageInstance = pageInstance
      this.wrapperStyle = wrapperStyle
    }

    render() {
      return (
        <div className="crm-dpl" style={ this.wrapperStyle } onClick={ e => e.stopPropagation() }>
          <div className="value">
            <input
              type="text"
              placeholder={ placeholder }
              readOnly
              { ...this.rest }
            />
            <a
              className="fa fa-angle-down"
              onClick={
                e => {
                  this.handleArrowClick(e, e.currentTarget.getAttribute('data-index'), e.currentTarget.getAttribute('data-api-id'))
                }
              }
              { ...this.rest }
            ></a>
          </div>
        </div>
      )
    }
  }
}

export default genPlainDropdown
