import React, { Component } from 'react'
import PropTypes from 'prop-types'
import { LinearProgress } from 'material-ui/Progress'
import ajax from '../../../lib/ajax'
import Tooltip from '../../widget/Tooltip/Tooltip'
import mapInfo from '../../mapInfo'
import TabLayout from './TabLayout'
import NavBar from './NavBar'

import './Index.styl'

function goToLogin() {
  localStorage.removeItem('9DAYE_CRM_TOKEN')
  localStorage.removeItem('9DAYE_CRM_USERNAME')
  location.href = '/login'
}

class Index extends Component {
  static propTypes = {
    mapInfo: PropTypes.object
  }

  constructor(props) {
    super(props)

    this.state = {
      login: false,
      Component: '<div></div>',

      toolTipText: '',
      toolTipType: '',
      toolTipDisplay: false,

      currentTab: '首页',
      currentPanel: mapInfo['首页'].component,
      progressDisplay: false,
      maskDisplay: false,

      userType: '0'
    }
  }

  componentDidMount() {
    this.isLogin()
  }

  showProgress() {
    this.setState({
      progressDisplay: true,
      maskDisplay: true
    })
  }
  hideProgress() {
    this.setState({
      progressDisplay: false,
      maskDisplay: false
    })
  }

  transferData(data) {
    this.setState({
      currentTab: data.tab,
      currentPanel: data.panel
    })
  }

  transferNavBarData(userType) {
    this.setState({ userType })
  }

  isLogin() {
    ajax({
      type: 'GET',
      url: '/authentication/account/is-login.do'
    })
      .then(info => {
        if (info.err) {
          this.showTip(info.desc, 'failed')
          if (info.goToLogin) {
            setTimeout(goToLogin, 3000)
          } else {
            location.href = '/login'
          }
        } else {
          this.setState({ login: true })
        }
      })
  }

  handleSubItemClick(tabName) {
    this.setState({
      currentTab: tabName,
      currentPanel: mapInfo[tabName].component
    })
  }

  handleLogoutClick() {
    this.showProgress()

    // 阻止更新状态
    this.setState({
      currentTab: null,
      currentPanel: null
    })

    ajax({
      type: 'GET',
      url: '/authentication/account/logout.do'
    })
      .then(() => {
        this.hideProgress()
        // 无论结果如何都转入登录页
        goToLogin()
      })
  }

  showTip(text, type) {
    this.setState({ toolTipDisplay: true, toolTipText: text, toolTipType: type })
    setTimeout(() => { this.hideTip() }, 2000)
  }

  hideTip() {
    this.setState({ toolTipDisplay: false })
  }

  render() {
    let ResultComponent = <div></div>

    const MainComponent = (
      <div className="crm-wrapper">

        <Tooltip
          text={ this.state.toolTipText }
          type={ this.state.toolTipType }
          show={ this.state.toolTipDisplay }
        />

        <LinearProgress
          classes={ {
            root: this.state.progressDisplay ? 'crm-progress crm-progress-show' : 'crm-progress',
            barColorPrimary: 'crm-progress-primary-color-bar',
            colorPrimary: 'crm-progress-primary-color'
          } }
        />

        <NavBar
          pageInstance={ this }
          onSubItemClick={ tabName => this.handleSubItemClick(tabName) }
          username={ localStorage.getItem('9DAYE_CRM_USERNAME') }
          onLogoutClick={ e => this.handleLogoutClick(e) }
          transferData={ type => this.transferNavBarData(type) }
        />

        <TabLayout
          userType={ this.state.userType }
          currentTab={ this.state.currentTab }
          currentPanel={ this.state.currentPanel }
          transferData={ data => this.transferData(data) }
        />

        <div className={ this.state.maskDisplay ? 'crm-mask crm-mask-show' : 'crm-mask' }></div>
      </div>
    )

    return this.state.login ? MainComponent : ResultComponent
  }
}

export default Index
