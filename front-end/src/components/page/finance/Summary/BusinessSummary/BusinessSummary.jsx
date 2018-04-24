/* global echarts */

import React, { Component } from 'react'
import ajax from '../../../../../lib/ajax'
import './BusinessSummary.styl'


class Cost extends Component {
  constructor(props) {
    super(props)
  }

  getData() {
    return ajax({
      method: 'GET',
      url: '/finance/get/turnover.do',
      data: {}
    }).then(info => {
      if (!info.err) {
        document.getElementsByClassName('count')[0].innerHTML = info.data.total
        // document.getElementsByClassName('count')[0].addEventListener( 'click', function () {
        //   document.querySelector('[data-id="施工统计"]').click()
        // })
        return info
      }
    })
  }

  componentDidMount() {
    this.getData().then((info) => {
      let obj = info.data.days
      let xdata = []
      let sdata = []
      for ( let index in obj ) {
        xdata.push(index.replace('2018-',''))
        sdata.push((obj[index]).split(',').join('') - 0)
      }
      let myChatrt = echarts.init(this.chartWrapper)
      let option = {
        tooltip: {},
        color: '#1e88e5',
        xAxis: {
          data: xdata,
          axisLabel: {
            interval: 0,
            rotate: -40
          }
        },
        grid: {
          y2: 140
        },
        yAxis: {
          type: 'value'
        },
        series: [ {
          type: 'line',
          data: sdata
        } ]
      }
      myChatrt.setOption(option)
    })
  }

  render() {
    return (
      <div className="crm-basic-page crm-finance-cost-page">
        <header>
          <div ref={ chartWrapper => this.chartWrapper = chartWrapper } style={ { width:"1000px",height:"500px",margin:"0 auto" } }></div>
        </header>
      </div>
    )
  }
}

class PurchaseAmount extends Component {
  constructor(props) {
    super(props)
  }

  getData() {
    return ajax({
      method: 'GET',
      url: '/finance/get/purchase-amount.do',
      data: {}
    }).then(info => {
      if (!info.err) {
        document.getElementsByClassName('count')[1].innerHTML = info.data.total
        // document.getElementsByClassName('count')[1].addEventListener( 'click',function () {
        //   document.querySelector('[data-id="采购统计汇总"]').click()
        // })
        return info
      }
    })
  }

  componentDidMount() {
    this.getData().then((info) => {
      let obj = info.data.months
      let xdata = []
      let sdata = []
      for ( let index in obj ) {
        xdata.push(index)
        sdata.push((obj[index]).split(',').join('') - 0)
      }
      let myChatrt = echarts.init(this.chartWrapper)
      let option = {
        tooltip: {},
        color: '#1e88e5',
        xAxis: {
          data: xdata
        },
        yAxis: {},
        series: [ {
          type: 'bar',
          data: sdata
        } ]
      }
      myChatrt.setOption(option)
    })
  }

  render() {
    return (
      <div className="crm-basic-page crm-finance-cost-page">
        <header>
          <div ref={ chartWrapper => this.chartWrapper = chartWrapper } style={ { width:"1000px",height:"500px",margin:"0 auto" } }></div>
        </header>
      </div>
    )
  }
}

class PassengerFlow extends Component {
  constructor(props) {
    super(props)
  }

  
  getData() {
    return ajax({
      method: 'GET',
      url: '/finance/get/frequency-of-store-visit.do',
      data: {}
    }).then(info => {
      if (!info.err) {
        document.getElementsByClassName('count')[2].innerHTML = info.data.total
        // document.getElementsByClassName('count')[2].addEventListener( 'click',function () {
        //   document.querySelector('[data-id="总到店台次"]').click()
        // })
        return info
      }
    })
  }

  componentDidMount() {
    this.getData().then((info) => {
      let obj = info.data.months
      let xdata = []
      let sdata = []
      for ( let index in obj) {
        xdata.push(index)
        sdata.push(obj[index])
      }
      let myChatrt = echarts.init(this.chartWrapper)
      let option = {
        tooltip: {},
        color: '#1e88e5',
        xAxis: {
          data: xdata
        },
        yAxis: {
        },
        series: [ {
          type: 'bar',
          data: sdata
        } ]
      }
      myChatrt.setOption(option)
    })
  }

  render() {
    return (
      <div className="crm-basic-page crm-finance-cost-page">
        <header>
          <div ref={ chartWrapper => this.chartWrapper = chartWrapper } style={ { width:"1000px",height:"500px",margin:"0 auto" } }></div>
        </header>
      </div>
    )
  }
}

class Member extends Component {
  constructor(props) {
    super(props)
  }

  getData() {
    return ajax({
      method: 'GET',
      url: 'finance/get/customers.do',
      data: {}

    }).then((info) => {
      if (!info.err) {
        document.getElementById('member').innerHTML = info.data.member
        document.getElementById('nonMember').innerHTML = info.data.non_member
        return info
      }
    })
  }

  componentDidMount() {
    this.getData().then((info) => {
      let obj = info.data.months
      let xdata = []
      let sdata = []
      for ( let index in obj ) {
        xdata.push(index)
        sdata.push(obj[index])
      }
      let myChatrt = echarts.init(this.chartWrapper)
      let option = {
        tooltip: {},
        color: '#1e88e5',
        xAxis: {
          data: xdata
        },
        yAxis: {
          type: 'value',
          max: 100
        },
        series: [ {
          type: 'bar',
          data: sdata
        } ]
      }
      myChatrt.setOption(option)
    })
  }

  render() {
    return (
      <div className="crm-basic-page crm-finance-cost-page">
        <header>
          <div ref={ chartWrapper => this.chartWrapper = chartWrapper } style={ { width:"1000px",height:"500px",margin:"0 auto" } }></div>
        </header>
      </div>
    )
  }
}

class chartWrapperList extends Component {

  constructor(props) {
    super(props)
    this.state = {
      tabs: [
        { tabName: '营业额统计（元）', id: 1 },
        { tabName: '采购金额（元）', id: 2 },
        { tabName: '总到店台次', id: 3 }
      ],
      currentIndex: 1
    }
  }

  componentDidMount () {

  }

  tabChoiced =(id) => {
    this.setState({
      currentIndex: id
    })
  }

  render() {
    let _this = this
    let isChart1Show = this.state.currentIndex == 1 ? 'block' : 'none'
    let isChart2Show = this.state.currentIndex == 2 ? 'block' : 'none'
    let isChart3Show = this.state.currentIndex == 3 ? 'block' : 'none'
    let isChart4Show = this.state.currentIndex == 4 ? 'block' : 'none'

    let tabList = this.state.tabs.map( (res,index) => {

      let tabStyle = res.id == this.state.currentIndex ? 'subCtrl active' : 'subCtrl'

      return <li key={ index } onClick={ this.tabChoiced.bind(_this,res.id) } className={ tabStyle } >
        <p className='title'>{res.tabName} </p>
        <a className="count"></a>
      </li>

    } )

    tabList[tabList.length] = (
      <li key={ 4 } onClick={ this.tabChoiced.bind(this, 4) } className={ this.state.currentIndex === 4 ? 'subCtrl active' : 'subCtrl' } >
        <p className='title'>客户（人）</p>
        <a className="memberbox">
          <p>散客：<span id="nonMember"></span></p>
          <p>会员：<span id="member"></span></p>
        </a>
      </li>
    )

    return (
      <div className="listWrap">
        <span className='dropdown'></span>
        <div className="listBox">
          <ul className="subNavWrap">
            {tabList}
          </ul>
          <div className="chartList">
            <div style={ { "display": isChart1Show } }>
              <p className="chartTitle">日营业额统计 <span className='crm-color crm-color-error'>(*显示日期按当天至前30天)</span></p>
              <Cost/>
            </div>
            <div style={ { "display": isChart2Show } }>
              <p className="chartTitle">月采购金额 <span className='crm-color crm-color-error'>(*显示日期按当月至前十二月)</span></p>
              <PurchaseAmount/>
            </div>
            <div style={ { "display": isChart3Show } }>
              <p className="chartTitle">月总到店台次走势图 <span className='crm-color crm-color-error'>(*显示日期按当月至前十二月)</span></p>
              <PassengerFlow/>
            </div>
            <div style={ { "display": isChart4Show } }>
              <p className="chartTitle">每月新增客户数量 <span className='crm-color crm-color-error'>(*显示日期按当月至前十二月)</span></p>
              <Member/>
            </div>
          </div>
        </div>

      </div>
    )
  }

}

export default chartWrapperList
