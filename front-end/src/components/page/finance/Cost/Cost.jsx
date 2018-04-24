/* global echarts */
import React, { Component } from 'react'
import Search from './Search'
import ajax from '../../../../lib/ajax'
import './Cost.styl'

class Cost extends Component {
  constructor(props) {
    super(props)

    this.state = {
      xdata: [],
      sdata: []
    }
  }



  componentDidMount() {
    this.getData({})
  }


  getData({ startDate = '', endDate = '', searchTime = '', supplier_id = '' }) {
    ajax({
      method: 'GET',
      url: '/finance/get/purchase-supplier.do',
      data: {
        startDate,
        endDate,
        searchTime,
        supplier_id
      }
    }).then(info => {
      let xdata = []
      let sdata = []
      let percent = 100
      for ( let i=0; i< info.data.length; i++ ) {
        xdata.push(info.data[i].supplier_name)
        sdata.push(Number(info.data[i].total_price))
      }
      percent = 100 / (info.data.length / 10)
      this.setState({
        xdata: xdata,
        sdata: sdata,
        percent: percent
      })
      let myChatrt = echarts.init(this.chartWrapper)
      let option = {
        title: {
          text: '采购图表'
        },
        color: '#3398DB',
        tooltip: {
          trigger: 'item'
        },
        grid: {
          y2: 200
        },
        xAxis: {
          data: this.state.xdata,
          axisLabel: {
            interval: 0,
            rotate: 0
          }
        },
        dataZoom: [
          {
            start: 0,
            end: this.state.percent,
            xAxisIndex: [ 0 ],
            type: 'slider',
            height: 20,
            show: true,
            backgroundColor: '#3398DB',
            handleSize: 20,
            showDetail: false,
            fillerColor: new echarts.graphic.LinearGradient(1, 0, 0, 0, [ {
              offset: 0,
              color: '#1eb5e5'
              },
              {
                  offset: 1,
                  color: '#5ccbb1'
              } ]),
          },
          {
            type: 'inside',
            xAxisIndex: [ 0 ],
            start: 1,
            end: 35
          },

        ],
        yAxis: {},
        series: [ {
          name: '供应商',
          type: 'bar',
          data: this.state.sdata,
          barMaxWidth: 50
        } ]
      }

      option.xAxis.axisLabel = {
        interval: 0,
        rotate: 0,
        formatter: function (params) {
          var newParamsValue = ''
          var paramsLen = params.length
          var maxLen = 6
          var rowNum = Math.ceil(paramsLen / maxLen)

          if (paramsLen > maxLen) {
            for (var p = 0; p < rowNum; p++) {
              var tempStr =''
              var start = p * maxLen
              var end = start + maxLen
              if (p == rowNum - 1) {
                tempStr = params.substring(start,paramsLen)
              } else {
                tempStr = params.substring(start,end) + '\n'
              }
              newParamsValue += tempStr
            }
          } else {
            newParamsValue = params
          }
          return newParamsValue
        }
      }
      myChatrt.setOption(option)
    })
  }

  render() {
    return (
      <div className="crm-basic-page crm-finance-cost-page" ref={ wrapper => this.wrapper = wrapper }>
        <header>
          <Search pageInstance={ this }/>
          <div ref={ chartWrapper => this.chartWrapper = chartWrapper } style={ { width:"1000px",height:"650px",margin:"0 auto",marginTop:"200px",marginBottom:"200px" } }></div>
        </header>
      </div>
    )
  }
}

export default Cost
