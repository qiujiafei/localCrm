import React, { Component } from 'react'
import { LinearProgress } from 'material-ui/Progress'
import Tooltip from '../../widget/Tooltip/Tooltip'
import ajax from '../../../lib/ajax'
import util from '../../../lib/util'

import './Login.styl'

class Login extends Component {
  constructor(props) {
    super(props)

    this.state = {
      // 提示框
      tooltipDisplay: false,
      tooltipText: '',
      tooltipType: '',

      // 进度条
      progressDisplay: false,

      // 遮罩
      maskDisplay: false
    }
  }

  componentDidMount() {
    util.clearLoginStatus()
  }

  showTip(text, type) {
    this.setState({ tooltipDisplay: true, tooltipText: text, tooltipType: type })

    setTimeout(() => {
      this.hideTip()
    }, 3000)
  }
  hideTip() {
    this.setState({ tooltipDisplay: false })
  }
  validate(form) {
    if (/^\s*$/.test(form.jsUsername.value)) {
      this.showTip('请输入用户名', 'failed')
      return false
    }

    if (/^\s*$/.test(form.jsUserPwd.value)) {
      this.showTip('请输入密码', 'failed')
      return false
    }

    return true
  }
  showProgress() {
    this.setState({ progressDisplay: true })
  }
  hideProgress() {
    this.setState({ progressDisplay: false })
  }

  handleSubmit(e) {
    e.preventDefault()
    const form = e.target

    if (this.validate(form)) {
      this.showProgress()

      ajax({
        method: 'POST',
        url: '/authentication/account/login.do',
        data: {
          username: form.jsUsername.value,
          passwd: form.jsUserPwd.value
        }
      })
        .then(info => {
          if (info.err) {
            this.showTip(info.desc, 'failed')
          } else {
            if (info.status == 200) {
              // 登录后信息记录到 sessionStorage
              localStorage.setItem('9DAYE_CRM_USERNAME', info.data.username)
              localStorage.setItem('9DAYE_CRM_TOKEN', info.data.token)
              location.href = '..'
            } else {
              this.showTip(info.data.errMsg, 'failed')
            }
          }

          this.hideProgress(this)
        })
    }
  }

  render() {
    return (
      <div className="crm-login">
        <Tooltip
          show={ this.state.tooltipDisplay }
          text={ this.state.tooltipText }
          type={ this.state.tooltipType }
        />

        <LinearProgress
          classes={ {
            root: this.state.progressDisplay ? 'crm-progress crm-progress-show' : 'crm-progress',
            barColorPrimary: 'crm-progress-primary-color-bar',
            colorPrimary: 'crm-progress-primary-color'
          } }
        />

        <div className={ this.state.maskDisplay ? 'crm-login-mask crm-login-mask-show' : 'crm-login-mask' }></div>

        <form className="crm-login-form" onSubmit={ e => this.handleSubmit(e) }>
          <header>
            <i className="crm-logo"></i>
          </header>
          <div className="crm-login-input">
            <input name="jsUsername" type="text" placeholder="请输入您的用户名" maxLength="20" />
          </div>
          <div className="crm-login-input">
            <input name="jsUserPwd" type="password" placeholder="请输入您的密码" maxLength="20" />
          </div>
          <footer>
            <button className="crm-login-submit-button" type="submit">登录</button>
          </footer>
        </form>
        <footer>
          <p>
            <span>九大爷平台：<a href="http://www.9daye.com.cn" title="点击进入九大爷平台">www.9daye.com.cn</a></span>
            <span>客服电话：400-0318-119</span>
          </p>
        </footer>
      </div>
    )
  }
}

export default Login
