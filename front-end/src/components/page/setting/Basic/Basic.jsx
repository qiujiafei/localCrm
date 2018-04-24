/* global _ */

import React, { Component } from 'react'
import PropTypes from 'prop-types'

import './Basic.styl'

class StoreBasicConfig extends Component {
  static propTypes = {
    mapInfo: PropTypes.object,
    transferData: PropTypes.func
  }

  constructor(props) {
    super(props)
  }

  handleItemClick(e) {
    e.preventDefault()

    const tab = e.currentTarget.getAttribute('data-name')

    this.props.transferData({
      tab,
      panel: this.props.mapInfo[tab].component
    })
  }

  getData(data) {
    const result = {
      '供应商管理': {},
      '商品信息管理': {},
      '仓库管理': {}
    }

    for (const name in data) {
      if (result[name]) {
        result[name] = {
          name: this.props.mapInfo[name]['name'],
          iconClass: this.props.mapInfo[name]['iconClass'],
          cnName: name
        }
      }
    }

    return result
  }

  render() {
    return (
      <div className="store-basic-config">
        <nav>
          {
            _.map(this.getData(this.props.mapInfo), (item, index) => {
              return (
                <a
                  key={ index }
                  onClick={ e => this.handleItemClick(e) }
                  data-name={ item.cnName }
                >
                  <i className={ 'icon icon-' + item.iconClass }></i>
                  <p>{ item.cnName }</p>
                </a>
              )
            })
          }
        </nav>
      </div>
    )
  }
}

export default StoreBasicConfig
