/*
 * 预览模态框 HOC 组件
 * Author: tanglijun
 * Date: 2018-02-22
 */

import React, { Component } from 'react'
import PropTypes from 'prop-types'

function genPreviewModalbox(option) {
  const {
    title = '',
    width = '500px',
    height = 'auto',
  } = option

  return class PreviewModalbox extends Component {
    static propTypes = {
      imgUrl: PropTypes.string,
      onCloseClick: PropTypes.func
    }

    static defaultProps = {
      imgUrl: 'http://www.9daye.com.cn/images/01-1-logo.jpg',
      onCloseClick: () => {}
    }

    constructor(props) {
      super(props)

      this.state = {
        imgUrl: props.imgUrl
      }
    }

    componentWillReceiveProps(nextProps) {
      this.setState(nextProps)
    }

    render() {
      return (
        <div className="crm-preview-modalbox">
          <div className="inner">
            <div className="box" style={ { width, height } }>
              <header>
                <span>{ title }</span>
                <button className="crm-icon crm-icon-close" title="关闭" onClick={ e => this.props.onCloseClick(e) }></button>
              </header>
              <div>
                <img src={ this.state.imgUrl } alt="身份证" />
              </div>
            </div>
          </div>
        </div>
      )
    }
  }
}

export default genPreviewModalbox
