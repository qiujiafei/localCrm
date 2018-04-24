import React, { Component } from 'react'
import PropTypes from 'prop-types'
import mapInfo from '../../mapInfo'

import './TabLayout.styl'

class TabLayout extends Component {

  static propTypes = {
    currentTab: PropTypes.string,
    currentPanel: PropTypes.func,
    transferData: PropTypes.func,
    userType: PropTypes.string
  }

  constructor(props) {
    super(props)

    this.tabs = {}
    this.tabs[props.currentTab] = props.currentPanel
    this.currentTab = props.currentTab
    this.currentPanel = props.currentPanel

    this.state = {
      userType: props.userType
    }
  }

  componentDidMount() {
    this.updateStyle()
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.currentTab && nextProps.currentPanel) {
      this.currentTab = nextProps.currentTab
      this.currentPanel = nextProps.currentPanel

      // 已有 tab 则更新样式
      if (!this.tabs[this.currentTab]) {
        this.tabs[this.currentTab] = this.currentPanel
        this.setState(nextProps)
      }
    }
  }

  componentDidUpdate() {
    this.updateStyle()
  }

  updateStyle() {
    const currentTab = this.layout.querySelector('.crm-tab-active')
    const currentPanel = this.layout.querySelector('.crm-panel-active')

    if (currentTab && currentPanel) {
      currentTab.className = 'crm-tab'
      currentPanel.className = 'crm-panel'
    }

    try {
      this.layout.querySelector('.crm-tab[data-name="' + this.currentTab + '"]').className = 'crm-tab crm-tab-active'
      this.layout.querySelector('.crm-panel[data-name="' + this.currentTab + '"]').className = 'crm-panel crm-panel-active'
    } catch (ignore) {
      this.layout.querySelector('.crm-tab:last-child').className = 'crm-tab crm-tab-active'
      this.layout.querySelector('.crm-panel:last-child').className = 'crm-panel crm-panel-active'
    }

    this.fixOffset()
  }

  fixOffset() {
    const tabWrapper = this.layout.querySelector('.crm-tabs')
    const currentTab = this.layout.querySelector('.crm-tab-active')
    const overflowWidth = currentTab.offsetLeft + currentTab.clientWidth - tabWrapper.clientWidth

    if (overflowWidth) {
      tabWrapper.scrollLeft = overflowWidth + currentTab.clientWidth / 2
    }
  }

  // 子模块中的数据传输接口
  transferData(data) {
    this.props.transferData(data)
  }

  handleCloseClick(e) {
    e.stopPropagation()

    const targetTab = e.currentTarget.getAttribute('data-name')

    if (targetTab !== '首页') {
      this.updateStyle()

      // 更新 tabs
      const newTabs = {}

      this.tabs[targetTab] = null

      for (const tab in this.tabs) {
        if (this.tabs[tab]) {
          newTabs[tab] = this.tabs[tab]
        }
      }

      this.tabs = newTabs

      this.setState({})
    }
  }

  // tab 切换
  handleTabClick(e) {
    e.preventDefault()

    const tab = e.currentTarget.getAttribute('data-name')
    const panel = this.tabs[tab]

    this.transferData({ tab, panel })
  }

  render() {
    const data = this.tabs
    const tabs = []
    const panels = []

    for (const tab in data) {
      const Panel = data[tab]

      if (Panel) {
        tabs.push(
          <a
            key={ tab }
            className="crm-tab"
            onClick={ e => this.handleTabClick(e) }
            data-name={ tab }
            title={ tab }
          >
            <div className="crm-tab-mask"></div>
            <i
              className={ 'crm-tab-icon ' + (tab === '首页' ? 'crm-tab-icon-home' : 'crm-tab-icon-close') }
              onClick={ e => this.handleCloseClick(e) }
              data-name={ tab }
              title="关闭"
            ></i>
            <span>{ tab }</span>
          </a>
        )

        panels.push(
          <div key={ tab } className="crm-panel" data-name={ tab }>
            <Panel userType={ this.state.userType } transferData={ data => this.transferData(data) } mapInfo={ mapInfo } { ...this.props } />
          </div>
        )
      }
    }

    return (
      <div className="crm-tab-layout" ref={ layout => this.layout = layout }>
        <header>
          <nav className="crm-tabs">{ tabs }</nav>
        </header>
        <div className="crm-panels">{ panels }</div>
      </div>
    )
  }
}

export default TabLayout
