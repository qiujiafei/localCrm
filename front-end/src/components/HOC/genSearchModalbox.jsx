/*
 * 搜索模态框 HOC 组件
 * Author: tanglijun
 * Date: 2018-02-13
 */

import React, { Component } from 'react'
import Pagination from '../widget/Pagination/Pagination'
import PropTypes from 'prop-types'
import ajax from '../../lib/ajax'
import genTable from './genTable'
import Indicator from '../widget/Indicator/Indicator'

function genSearchModalbox(options) {
  const {
    width = '500px',
    height = '400px',
    title = '',
    columns = [],
    apiInfo = {}
  } = options

  // 添加序号
  columns.unshift({
    Header: '序号',
    accessor: 'no',
    width: 40
  })

  return class SearchModalbox extends Component {
    static propTypes = {
      parentPage: PropTypes.object,
      transferTrData: PropTypes.func,
      handleClose: PropTypes.func
    }

    constructor(props) {
      super(props)

      this.state = {
        // 加载动画
        indicatorDisplay: true,

        data: [],
        show: true,
        pageSize: 15,
        current: 1,
        totalCount: 0
      }
    }

    componentDidMount() {
      this.getData()
    }

    getData() {
      this.showIndicator()

      return ajax({
        method: 'GET',
        url: apiInfo.url,
        data: apiInfo.data
      }).then(info => {
        if (info.err) {
          this.props.parentPage.showTip(info.desc, 'failed')

          if (info.goToLogin) {
            setTimeout(() => {
              location.href = '/login'
            }, 3000)
          }
        } else {
          this.setState({
            totalCount: Number(info.data.total_count) || 0,
            data: this.formateData(info.data[apiInfo.listKey] || [])
          })
        }

        this.hideIndicator()
      })
    }

    formateData(data) {
      data.forEach((item, index) => {
        // 添加序号
        item.no = ((index + 1) + (this.state.current - 1) * this.state.pageSize).toString()
      })

      return data
    }

    showIndicator() {
      this.setState({ indicatorDisplay: true })
    }

    hideIndicator() {
      this.setState({ indicatorDisplay: false })
    }

    handleTrClick(data) {
      this.props.transferTrData(data)
    }

    handleBoxClose() {
      apiInfo.data.page = apiInfo.data.page_num = 1
      this.setState({ show: false })
      this.props.handleClose()
    }

    render() {

      const Table = genTable({
        columns: columns.slice(),
        data: this.state.data
      })

      const CrmSearchModalbox = (
        <div className="crm-search-modalbox">

          <div className="inner">
            <div className="box" style={ { width, height } }>
              <Indicator
                show={ this.state.indicatorDisplay }
              />

              <header>
                <span>{ title }</span>
                <a className="crm-icon crm-icon-close" title="关闭" onClick={ e => this.handleBoxClose(e) }></a>
              </header>

              <Table
                getTrProps={
                  (state, rowInfo, column) => {
                    return {
                      style: { cursor: 'pointer' },
                      onClick: e => this.handleTrClick({ e, state, rowInfo, column })
                    }
                  }
                }
                getTdProps={
                  (state, rowInfo, column) => {
                    let title = ''

                    if (rowInfo) {
                      title = rowInfo.row[column.id]
                    }

                    return {
                      title
                    }
                  }
                }
              />

              <footer>
                <Pagination
                  pageSize={ this.state.pageSize }
                  current={ this.state.current }
                  total={ this.state.totalCount }
                  onChange={ current => {
                    this.setState({ current })
                    apiInfo.data.page = current
                    apiInfo.data.page_num = current
                    this.getData()
                  } }
                />
              </footer>
            </div>
          </div>
        </div>
      )

      return this.state.show ? CrmSearchModalbox : null
    }
  }
}

export default genSearchModalbox
