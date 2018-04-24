import React, { Component } from 'react'
import Indicator from '../../widget/Indicator/Indicator'
import genBasicModalbox from '../../HOC/genBasicModalbox'
import ajax from '../../../lib/ajax'

import './ProdGroup2Modalbox.styl'

class BodyComponent extends Component {
  constructor(props) {
    super(props)

    this.state = {

      groupAData: [],
      groupBData: [],

      // 加载动画
      groupAIndicatorDisplay: false,
      groupBIndicatorDisplay: false
    }
  }

  componentDidMount() {
    this.getGroupA()
  }

  clear() {
    const element = this.wrapper.querySelector('li.on')

    if (element) {
      element.removeAttribute('class')
    }
  }

  getGroupA() {
    this.showIndicator('A')

    ajax({
      method: 'GET',
      url: '/classification/get/getall.do',
      data: {
        depth: [ 1 ]
      }
    }).then(info => {
      if (info.err) {
        this.handelApiError(info)
      } else {
        this.setState({ groupAData: info.data })
      }

      this.hideIndicator('A')
    })
  }

  getGroupB(parent_id) {
    this.showIndicator('B')

    ajax({
      method: 'GET',
      url: '/classification/get/getall.do',
      data: {
        parent_id,
        depth: [ 2 ]
      }
    }).then(info => {
      if (info.err) {
        this.handelApiError(info)
      } else {
        const data = info.data.filter(item => item.id !== sessionStorage.getItem('EDITID'))
        this.setState({ groupBData: [] }, () => this.setState({ groupBData: data }))
      }

      this.hideIndicator('B')
    })
  }

  handelApiError(info) {
    this.showTip(info.desc, 'failed')

    if (info.goToLogin) {
      setTimeout(() => {
        location.href = '/login'
      }, 3000)
    }
  }

  showIndicator(type) {
    this.setState({ ['group' + type + 'IndicatorDisplay']: true })
  }

  hideIndicator(type) {
    this.setState({ ['group' + type + 'IndicatorDisplay']: false })
  }

  render() {
    return (
      <div className="prod-group2-modalbox" ref={ wrapper => this.wrapper = wrapper }>
        <div className="cell">
          <div className="inner">
            <header>一级分类</header>
            { this.state.groupAData.length > 0 ? (
              <ul>
                { this.state.groupAData.map((item, index) => {
                  return (
                    <li
                      key={ index }
                      data-id={ item.id }
                      title={ item.classification_name }
                      onClick={ e => {
                        this.clear()
                        e.currentTarget.className = 'on'
                        this.getGroupB(e.currentTarget.getAttribute('data-id'))
                      } }
                    >
                      <i className="fa fa-folder"></i>
                      <span>{ item.classification_name }</span>
                    </li>
                  )
                }) }
              </ul>
            ) : <div>暂无数据</div> }
            <Indicator show={ this.state.groupAIndicatorDisplay } />
          </div>
        </div>

        <div className="cell">
          <div className="inner">
            <header>二级分类</header>
            { this.state.groupBData.length > 0 ? (
              <ul>
                { this.state.groupBData.map((item, index) => {
                  return (
                    <li
                      key={ index }
                      data-id={ item.id }
                      title={ item.classification_name }
                      onClick={ e => {
                        this.clear()
                        e.currentTarget.className = 'on'
                      } }
                    >
                      <i
                        className="fa fa-folder"
                      ></i>
                      <span>{ item.classification_name }</span>
                    </li>
                  )
                }) }
              </ul>
            ) : <div>暂无数据</div> }
            <Indicator show={ this.state.groupBIndicatorDisplay } />
          </div>
        </div>
      </div>
    )
  }
}

export default genBasicModalbox({
  name: '商品分类',
  width: '800',
  height: '600',
  BodyComponent
})
