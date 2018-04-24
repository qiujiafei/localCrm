/* global $ */

import React, { Component } from 'react'
import PropTypes from 'prop-types'
import ajax from '../../../lib/ajax'

class Dropdown extends Component {
  static propTypes = {
    api: PropTypes.string,
    dataKey: PropTypes.string,
    dataId: PropTypes.string,
    dataName: PropTypes.string,
    onItemClick: PropTypes.func,
    onArrowClick: PropTypes.func,
    pageInstance: PropTypes.object,
    transferData: PropTypes.func,
    noPage: PropTypes.bool,
    wrapperStyle: PropTypes.object
  }

  static defaultProps = {
    onItemClick: () => {},
    onArrowClick: () => {}
  }

  constructor(props) {
    super(props)

    const {
      api,
      dataKey,
      dataId,
      dataName,
      pageInstance,
      transferData,
      noPage,
      wrapperStyle,
      ...rest
    } = props

    this.state = {
      data: []
    }

    this.api = api
    this.dataKey = dataKey
    this.dataId = dataId
    this.dataName = dataName
    this.rest = rest
    this.pageInstance = pageInstance
    this.transferData = transferData
    this.noPage = noPage
    this.wrapperStyle = wrapperStyle

    this.show = this.show.bind(this)
    this.hide = this.hide.bind(this)
    this.scroll = this.scroll.bind(this)
  }

  componentDidMount() {
    this.calcPosition()
    window.addEventListener('click', this.hide)
    $('.crm-panel.crm-panel-active').on('scroll', this.scroll)
  }

  componentWillUnmount() {
    window.removeEventListener('click', this.hide)
    $('.crm-panel.crm-panel-active').unbind('scroll')
  }

  componentWillReceiveProps(nextProps) {
    this.setState(nextProps)
  }

  scroll() {
    if ($(this.wrapper).offset()) {
      this.calcPosition()
    }
  }

  show() {
    this.list.className += ' show'
  }

  hide() {
    this.list.className = this.list.className.replace(/\s?show\s?/, '')
  }

  calcPosition() {
    // position: fixed 时计算位置
    const wrapperOffsetTop = $(this.wrapper).offset().top
    const wrapperHeight = this.wrapper.offsetHeight
    const winHeight = $(window).height()
    const listOffsetHeight = 200

    if (this.list.style.position === 'fixed') {
      if (wrapperOffsetTop + wrapperHeight + listOffsetHeight > winHeight) {
        this.list.style.top = wrapperOffsetTop - listOffsetHeight + 'px'
      } else {
        this.list.style.top = wrapperOffsetTop + wrapperHeight + 'px'
      }
    }

    this.list.style.width = this.wrapper.clientWidth + 'px'
  }

  onItemClick(e, callback) {
    const key = e.currentTarget.getAttribute('data-key')
    const name = e.currentTarget.getAttribute('data-name')

    this.ipt.setAttribute('data-key', key)
    this.ipt.value = name
    this.hide()

    callback(key, name)
  }

  onArrowClick(e, callback) {
    e.preventDefault()

    this.calcPosition()

    this.getData()

    if (this.list.className.match('show')) {
      this.hide()
    } else {
      this.show()
    }

    callback()
  }

  getData() {
    let data = {
      count_per_page: 999,
      page_num: 1
    }

    if (this.noPage) {
      data = {}
    }

    ajax({
      method: 'GET',
      url: this.props.api,
      data
    })
      .then(info => {
        if (info.err) {
          if (info.goToLogin) {
            this.pageInstance.showToolTip('登录超时', 'failed')

            setTimeout(() => {
              location.href = '/login'
            }, 3000)
          }
        } else {
          this.setState({ data: this.formatData(info.data[this.dataKey] || info.data) })
        }
      })
  }

  formatData(data) {
    const result = []

    for (let i = 0; i < data.length; i += 1) {
      const item = data[i]

      result.push({
        id: item[this.dataId],
        name: item[this.dataName]
      })
    }

    return result
  }

  render() {

    const { onArrowClick, onItemClick, style, ...rest } = this.rest

    return (
      <div className="crm-dpl" style={ this.wrapperStyle } onClick={ e => e.stopPropagation() } ref={ wrapper => this.wrapper = wrapper } >
        <div className="value">
          <input type="text" readOnly ref={ ipt => this.ipt = ipt } { ...rest } />
          <a className="fa fa-angle-down" onClick={ e => this.onArrowClick(e, onArrowClick) }></a>
        </div>
        <div className="list" ref={ list => this.list = list } style={ style }>
          {
            this.state.data.length > 0 ? (
              <ul>
                {
                  this.state.data.map((item, index) => {
                    return (
                      <li data-key={ item.id } data-name={ item.name } key={ index } onClick={ e => this.onItemClick(e, onItemClick) }>
                        { item.name }
                      </li>
                    )
                  })
                }
              </ul>
            ) : <div>加载中...</div>
          }
        </div>
      </div>
    )
  }
}

export default Dropdown
