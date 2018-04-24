import React, { Component } from 'react'
import PropTypes from 'prop-types'
import items from './items'
import Deny from './Deny'
import ajax from '../../../../lib/ajax'

import './Home.styl'

class Home extends Component {
  static propTypes = {
    userType: PropTypes.string,
    mapInfo: PropTypes.object,
    transferData: PropTypes.func
  }

  constructor(props) {
    super(props)

    this.state = {}
  }

  componentWillReceiveProps(nextProps) {
    this.setState({
      userType: nextProps.userType
    })
  }

  onHomeItemClick(name) {
    this.props.transferData({ tab: name, panel: this.props.mapInfo[name].component })
  }

  componentDidMount() {
    ajax({
      method: 'GET',
      url: '/frontBridge/get/profile.do'
    }).then((info) => {
      const items = this.wrapper.querySelectorAll('.itemValue')

      if (items && items.length) {
        items[0].innerHTML = info.data.custom_in_bill.member_count
        items[1].innerHTML = info.data.custom_in_bill.traveler_count
        items[2].innerHTML = info.data.today_finance_profile
        items[3].innerHTML = info.data.custom
        items[4].innerHTML = info.data.custom_month_increse
      }
    })
  }

  render() {
    if (this.state.userType == 0) {
      return <Deny />
    } else {
      if (this.state.userType == 1) {
        return (
          <div className="crm-home" ref={ wrapper => this.wrapper = wrapper }>
            <div className="inner">
              <div className="grid-list">
                {
                  items.map((item, index) => {
                    return (
                      <a className="home-item" key={ index } title={ item.name } onClick={ () => this.onHomeItemClick(item.name) }>
                        <div>
                          <header>
                            <h1>{ item.name }</h1>
                            <p>{ item.englishName }</p>
                          </header>
                          <div className="item-body">
                            <ul className="list">
                              {
                                item.items.map((subItem, index) => {
                                  return (
                                    <li key={ index }>{ subItem }: <span className="itemValue">0</span></li>
                                  )
                                })
                              }
                            </ul>
                            <div className={ "item-icon item-icon-" + (index + 1) }></div>
                          </div>
                        </div>
                      </a>
                    )
                  })
                }
              </div>
            </div>
          </div>
        )
      }

      return <div></div>
    }
  }
}

export default Home
