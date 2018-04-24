/*
 * 高级模态框 HOC 组件
 * Author: tanglijun
 * Date: 2018-02-13
 */

import React, { Component } from 'react'
import PropTypes from 'prop-types'
import InputText from '../widget/form/InputText'
import Button from '../widget/Button'
import genTable from './genTable'
import ajax from '../../lib/ajax'
import Pagination from '../widget/Pagination/Pagination'
import Indicator from '../widget/Indicator/Indicator'
import Tooltip from '../widget/Tooltip/Tooltip'

function genAdvancedModalbox(options) {

  const {
    title = "标题",
    width = 'auto',
    height = 'auto',
    displayOrderNum = true,
    displayLineBtn = false,
    columns = [],
    apiInfo = {},
    Header,
    onDelete = () => {},
    onEdit = () => {}
  } = options

  if (displayOrderNum) {
    columns.unshift({
      Header: '序号',
      accessor: 'no',
      width: 40
    })
  }

  if (displayLineBtn) {
    columns.push({
      Header: '操作',
      accessor: 'control',
      width: 120
    })
  }

  return class AdvancedModalbox extends Component {
    static propTypes = {
      parentPage: PropTypes.object,
      transferTrData: PropTypes.func,
      handleClose: PropTypes.func
    }

    constructor(props) {
      super(props)

      this.state = {

        // 提示框
        tooltipDisplay: false,
        tooltipText: '',
        tooltipType: '',

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
            data: this.formateData(info.data[apiInfo.listKey] || info.data || [])
          })
        }

        this.hideIndicator()
      })
    }

    showIndicator() {
      this.setState({ indicatorDisplay: true })
    }

    hideIndicator() {
      this.setState({ indicatorDisplay: false })
    }

    showToolTip(tooltipText, tooltipType) {
      this.setState({ tooltipDisplay: true, tooltipText, tooltipType })

      setTimeout(() => {
        this.hideToolTip()
      }, 2500)
    }

    hideToolTip() {
      this.setState({ tooltipDisplay: false })
    }

    formateData(data) {
      data.forEach((item, index) => {

        // 添加序号
        item.no = ((index + 1) + (this.state.current - 1) * this.state.pageSize).toString()

        // 添加操作
        item.control = (
          <div>
            <Button data-id={ item.id } style={ { width: 'auto', height: 'auto' } } text="修改" type="link" onClick={ (e) => this.onEdit(e) } />
            <span>|</span>
            <Button data-id={ item.id } style={ { width: 'auto', height: 'auto' } } text="删除" type="link" onClick={ (e) => this.onDelete(e) } />
          </div>
        )
      })

      return data
    }

    onEdit(e) {
      e.stopPropagation()
      onEdit(e, this.props.parentPage, this)
    }

    onDelete(e) {
      e.stopPropagation()
      onDelete(e, this.props.parentPage, this)
    }

    handleTrClick(data) {
      this.props.transferTrData(data)
    }

    handleBoxClose() {
      this.setState({ show: false })
      this.props.handleClose()
    }

    handleSearch() {
      apiInfo.data.keyWord =
      apiInfo.data.keyword =
        this.wrapper.querySelector('.crm-input input').value

      apiInfo.data.page = 1

      this.getData()

      // 查询后清空
      apiInfo.data.keyWord = apiInfo.data.keyword = ''
    }

    render() {
      const Table = genTable({
        columns: columns.slice(),
        data: this.state.data
      })

      return (
        <div className="crm-advanced-modalbox" ref={ wrapper => this.wrapper = wrapper }>

          <Tooltip show={ this.state.tooltipDisplay } text={ this.state.tooltipText } type={ this.state.tooltipType } />

          <div className="inner">
            <div className="box" style={ { width, height } }>
              <header>
                <span>{ title }</span>
                <button className="crm-icon crm-icon-close" onClick={ e => this.handleBoxClose(e) } title="关闭"></button>
              </header>
              <div className="body">
                <div className="left">
                  <Indicator
                    show={ this.state.indicatorDisplay }
                  />

                  <header>
                    {
                      Header ? <Header apiInfo={ apiInfo } pageInstance={ this } /> : (
                        <div>
                          <InputText text="关键字" />
                          <Button text="查询" type="secondary" onClick={ e => this.handleSearch(e) } />
                        </div>
                      )
                  }
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
                    {
                      this.state.totalCount ? <Pagination
                        pageSize={ this.state.pageSize }
                        current={ this.state.current }
                        total={ this.state.totalCount }
                        onChange={ current => {
                          this.setState({ current })
                          apiInfo.data.page = current
                          apiInfo.data.page_num = current
                          this.getData()
                        } }
                      /> : null
                    }
                  </footer>
                </div>
                <div className="right"></div>
              </div>
            </div>
          </div>
        </div>
      )
    }
  }
}

export default genAdvancedModalbox
