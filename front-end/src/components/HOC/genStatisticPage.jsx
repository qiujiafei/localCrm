/* global _ */
import React, { Component } from 'react'
import Indicator from '../widget/Indicator/Indicator'
import Tooltip from '../widget/Tooltip/Tooltip'
import ajax from '../../lib/ajax'

function genStatisticPage({ Search, apiInfo }) {
  return class Statistic extends Component {
    constructor(props) {
      super(props)

      this.state = {

        // 提示框
        toolTipText: '',
        toolTipType: '',
        toolTipDisplay: false,

        // 加载动画
        indicatorDisplay: false,

        data: apiInfo.defaultData
      }
    }

    componentDidMount() {
      this.init()
    }

    async init() {
      this.showIndicator()

      const data = await this.getData()

      this.setState({ data }, this.hideIndicator)

      this.hideIndicator()
    }

    showIndicator() {
      this.setState({ indicatorDisplay: true })
    }

    hideIndicator() {
      this.setState({ indicatorDisplay: false })
    }

    getData() {
      return ajax({
        method: 'GET',
        data: apiInfo.data || {},
        url: apiInfo.url
      }).then(info => {
        const data = this.state.data

        if (info.err) {
          this.handleError(info)
        } else {
          _.each(info.data, (value, key) => {
            data[key].value = value
          })
        }

        return data
      })
    }

    handleError(info) {
      this.showTooltip(info.desc, 'failed')

      if (info.goToLogin) {
        setTimeout(() => {
          location.href = '/login'
        }, 3000)
      }
    }

    showTooltip(toolTipText, toolTipType) {
      this.setState({ toolTipDisplay: true, toolTipText, toolTipType })

      setTimeout(() => {
        this.hideTooltip()
      }, 3000)
    }

    hideTooltip() {
      this.setState({ toolTipDisplay: false })
    }

    render() {
      return (
        <div className="crm-statistic-page" ref={ wrapper => this.wrapper = wrapper }>

          <Tooltip
            text={ this.state.toolTipText }
            type={ this.state.toolTipType }
            show={ this.state.toolTipDisplay }
          />

          <Indicator
            show={ this.state.indicatorDisplay }
          />

          { Search && <header><Search pageInstance={ this } apiInfo={ apiInfo } /></header> }

          <table>
            <tbody>
              <tr>
                {
                  _.map(this.state.data, (item, key) => {
                    return (
                      <td key={ key }>
                        <div>
                          <span>{ item.value } { apiInfo.unit }</span>
                          <p>{ item.key }</p>
                        </div>
                      </td>
                    )
                  })
                }
              </tr>
            </tbody>
          </table>
        </div>
      )
    }
  }
}

export default genStatisticPage
