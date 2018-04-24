import React, { Component } from 'react'
import Indicator from '../../widget/Indicator/Indicator'
import genBasicModalbox from '../../HOC/genBasicModalbox'
import ajax from '../../../lib/ajax'

import './ProdGroupModalbox.styl'

class BodyComponent extends Component {
  constructor(props) {
    super(props)

    this.state = {

      groupAData: [],
      groupBData: [],
      groupCData: [],

      // 加载动画
      groupAIndicatorDisplay: false,
      groupBIndicatorDisplay: false,
      groupCIndicatorDisplay: false
    }
  }

  componentDidMount() {
    this.getGroupA()
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
        this.setState({ groupBData: [], groupCData: [] }, () => this.setState({ groupBData: info.data }))
      }

      this.hideIndicator('B')
    })
  }

  getGroupC(parent_id) {
    this.showIndicator('C')

    ajax({
      method: 'GET',
      url: '/classification/get/getall.do',
      data: {
        parent_id,
        depth: [ 3 ]
      }
    }).then(info => {
      if (info.err) {
        this.handelApiError(info)
      } else {
        this.setState({ groupCData: [] }, () => this.setState({ groupCData: info.data }))
      }

      this.hideIndicator('C')
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
      <div className="prod-group-modalbox" ref={ wrapper => this.wrapper = wrapper }>
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
                        const prevElement = e.currentTarget.parentNode.querySelector('li.on')

                        if (prevElement) {
                          prevElement.removeAttribute('class')
                          prevElement.querySelector('.fa').className = 'fa fa-folder'
                        }

                        e.currentTarget.className = 'on'
                        e.currentTarget.querySelector('.fa').className = 'fa fa-folder-open'
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
                        const prevElement = e.currentTarget.parentNode.querySelector('li.on')

                        if (prevElement) {
                          prevElement.removeAttribute('class')
                          prevElement.querySelector('.fa').className = 'fa fa-folder'
                        }

                        e.currentTarget.className = 'on'
                        e.currentTarget.querySelector('.fa').className = 'fa fa-folder-open'
                        this.getGroupC(e.currentTarget.getAttribute('data-id'))
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

        <div className="cell">
          <div className="inner">
            <header>三级分类</header>
            { this.state.groupCData.length > 0 ? (
              <ul>
                { this.state.groupCData.map((item, index) => {
                  return (
                    <li
                      key={ index }
                      data-id={ item.id }
                      title={ item.classification_name }
                      onClick={ e => {
                        const prevElement = e.currentTarget.parentNode.querySelector('li.on')

                        if (prevElement) {
                          prevElement.removeAttribute('class')
                        }

                        e.currentTarget.className = 'on'
                      } }
                    >
                      <i
                        className="fa fa-file-o"
                      ></i>
                      <span>{ item.classification_name }</span>
                    </li>
                  )
                }) }
              </ul>
            ) : <div>暂无数据</div> }
            <Indicator show={ this.state.groupCIndicatorDisplay } />
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
