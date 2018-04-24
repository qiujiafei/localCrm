import React, { Component } from 'react'
import PropTypes from 'prop-types'
import ajax from '../../../lib/ajax'

import './NavBar.styl'

class NavBar extends Component {

  static propTypes = {
    pageInstance: PropTypes.object,
    onSubItemClick: PropTypes.func,
    onLogoutClick: PropTypes.func,
    username: PropTypes.string,
    transferData: PropTypes.func
  }

  constructor(props) {
    super(props)

    this.state = {
      username: props.username,
      menus: []
    }
  }

  componentDidMount() {
    this.getData()
  }

  getData() {
    ajax({
      method: 'GET',
      url: '/authorization/menu/getall.do'
    }).then(info => {
      if (info.err) {
        this.props.pageInstance.showTip(info.desc, 'failed')

        if (info.goToLogin) {
          setTimeout(() => {
            location.href = '/login'
          }, 3000)
        }

      } else {
        this.setState({
          menus: info.data.menu
        })
        this.props.transferData(info.data.type.toString())
      }
    })
  }

  componentWillReceiveProps(nextProps) {
    this.setState(nextProps)
  }

  handleItemClick(e) {
    if (typeof this.props.onSubItemClick === 'function') {
      this.props.onSubItemClick(e.target.innerHTML)
    }
  }

  handleLogoutClick(e) {
    e.preventDefault()

    if (typeof this.props.onLogoutClick === 'function') {
      this.props.onLogoutClick()
    }
  }

  render() {
    return (
      <header className="crm-nav-bar">
        <span className="title"><i className="logo"></i><small>（建议不要使用 IE 浏览器进行登录）</small></span>
        <div className="menu">
          <nav className="list">
            <ul>
              { this.state.menus.map((item, index) => {
                return (
                  <li key={ index }>
                    <a>{ item.name }</a>
                    { item.children.length > 0 && (
                      <ul>
                        {
                          item.children.map((subItem, index) => {
                            return (
                              <li key={ index } onClick={ e => this.handleItemClick(e) }>{ subItem.name }</li>
                            )
                          })
                        }
                      </ul>
                    ) }
                  </li>
                )
              }) }
            </ul>
          </nav>
          <div className="user">
            <i className="fa fa-user"></i>
            <span>{ this.state.username }</span>
            <span>|</span>
            <span>
              <a
                href="#"
                title="退出"
                onClick={ e => this.handleLogoutClick(e) }
              >退出</a>
            </span>
          </div>
        </div>
      </header>
    )
  }
}

export default NavBar
