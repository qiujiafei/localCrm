/* global Promise _ */

import React, { Component } from 'react'
import Tooltip from '../widget/Tooltip/Tooltip'
import Indicator from '../widget/Indicator/Indicator'
import util from '../../lib/util'
import genTable from './genTable'

function genBillPage(options) {
  const {
    columns,
    Header,
    Footer,
    Calculation,
    ProdModalbox,
    DepotModalbox,
    onInputClick,
    onInputBlur,
    displayBatchBtn = true,
    noDefaultData = false
  } = options

  return class BillPage extends Component {
    constructor(props) {
      super(props)

      this.state = {

        // 提示框
        tooltipDisplay: false,
        tooltipText: '',
        tooltipType: '',

        // 选择仓库模态框
        depotModalboxDisplay: false,

        // 选择商品模态框
        prodModalboxDisplay: false,

        // 加载动画
        indicatorDisplay: false,

        // 表格数据
        columns: [],
        data: [],

        totalPrice: 0
      }

      this.apiData = []
    }

    componentDidMount() {
      this
        .initColumns()
        .then(() => {
          !noDefaultData && this.createDefaultData()
        })
    }

    componentDidUpdate() {

      // 填充数据
      this.apiData.forEach((item, index) => {
        for (const key in item) {
          const element = this.billPage.querySelectorAll('.body input[data-api-id="' + key + '"]')[index]

          element && (element.value = item[key])

          this.statistics(key)
        }
      })
    }

    handleArrowClick(type, id, index, e) {
      this.type = type

      switch (type) {
        case '商品':
        case '批次商品':
          this.showProdModalbox(id, index, e)
          break
        case '仓库':
          this.showDepotModalbox(id, index, e)
          break
      }

      this.index = index - 1
      this.id = id
    }

    showProdModalbox() {
      this.setState({ prodModalboxDisplay: true })
    }

    hideProdModalbox() {
      this.setState({ prodModalboxDisplay: false })
    }

    showDepotModalbox() {
      this.setState({ depotModalboxDisplay: true })
    }

    hideDepotModalbox() {
      this.setState({ depotModalboxDisplay: false })
    }

    transferTrData(data) {
      const row = data.rowInfo.original

      this.apiData[this.index][this.id] = row[this.id]

      _.each(row, (item, key) => {
        switch (this.type) {
          case '商品':
            if (key === 'id') {
              this.apiData[this.index]['commodity_id'] = item
              this.apiData[this.index]['commodity_batch_id'] = item
            } else if (key === 'stock') {
              this.apiData[this.index]['inventory_quantity'] = item
            }

            this.apiData[this.index]['last_purchase_price'] = '0.00'
            break
          case '仓库':
            this.apiData[this.index]['depot_id'] = row.id
            break
        }

        if (key !== 'comment') {
          this.apiData[this.index][key] = item
        }
      })

      this.hideDepotModalbox()
      this.hideProdModalbox()
    }

    genRows(index, data) {
      const result = {}

      if (!this.apiData[index - 1]) {
        this.apiData[index - 1] = {}
      }

      _.each(this.state.columns, column => {

        if (data) {
          this.apiData[index - 1][column.accessor] = data[column.accessor] || ''
        }

        if (column.accessor === 'no') {
          result[column.accessor] = index
        } else {
          if (column.widgetPath) {
            const Component = require('../widget/' + column.widgetPath)['default']

            if (column.required) {
              result[column.accessor] = (
                <div data-id={ column.accessor }>
                  <Component
                    data-index={ index }
                    data-api-id={ column.accessor }
                    handleArrowClick={
                      (e, index, id) => this.handleArrowClick(column.Header, id, index, e)
                    }
                    required
                  />
                </div>
              )
            } else {
              result[column.accessor] = (
                <div data-id={ column.accessor }>
                  <Component
                    data-id={ column.accessor }
                    data-index={ index }
                    data-api-id={ column.accessor }
                    handleArrowClick={
                      (e, index, id) => this.handleArrowClick(column.Header, id, index, e)
                    }
                  />
                </div>
              )
            }

          } else {
            if (column.editable) {
              if (column.required) {
                result[column.accessor] = (
                  <div data-id={ column.accessor }>
                    <input
                      className="text-field"
                      data-index={ index }
                      data-api-id={ column.accessor }
                      onInput={ e => onInputClick ? onInputClick(e, column, this) : this.handleCellInput(e, column) }
                      onBlur={ e => onInputBlur ? onInputBlur(e, column, this) : this.handleCellBlur(e, column) }
                      placeholder={ '请输入' + column.Header }
                      required
                    />
                  </div>
                )
              } else {
                result[column.accessor] = (
                  <div data-id={ column.accessor }>
                    <input
                      className="text-field"
                      data-index={ index }
                      data-api-id={ column.accessor }
                      onInput={ e => onInputClick ? onInputClick(e, column, this) : this.handleCellInput(e, column) }
                      onBlur={ e => onInputBlur ? onInputBlur(e, column, this) : this.handleCellBlur(e, column) }
                      placeholder={ '请输入' + column.Header }
                    />
                  </div>
                )
              }
            } else {
              result[column.accessor] = (
                <input
                  style={ { backgroundColor: 'transparent', paddingLeft: '10px', paddingRight: '10px' } }
                  data-index={ index }
                  data-api-id={ column.accessor }
                  defaultValue={ data && data[column.accessor] }
                  readOnly
                />
              )
            }
          }
        }
      })

      return result
    }

    initColumns() {
      return new Promise(resolve => {
        const newColumns = columns.slice()

        // 添加操作字段
        if (displayBatchBtn) {
          newColumns.unshift({
            Header: '操作',
            accessor: 'interaction',
            noTitle: true,
            Cell: (cell) => {
              return (
                <div>
                  <a
                    style={ {
                      fontSize: '20px',
                      color: '#888',
                      marginTop: '7px',
                      marginRight: '7px',
                      cursor: 'pointer'
                    } }
                    className="fa fa-plus"
                    title="添加"
                    onClick={ e => this.handleAddRow(e) }
                    data-index={ cell.index }
                  ></a>
                  <a
                    style={ {
                      fontSize: '20px',
                      color: '#888',
                      marginTop: '7px',
                      cursor: 'pointer'
                    } }
                    className="fa fa-trash"
                    title="删除"
                    onClick={ e => this.handleDelRow(e) }
                    data-index={ cell.index }
                  ></a>
                </div>
              )
            },
            Footer: () => {
              return (
                <div>合计</div>
              )
            }
          })
        } else {
          newColumns[0].Footer = () => <div>合计</div>
        }

        // 添加序号字段
        newColumns.unshift({
          Header: '序号',
          accessor: 'no',
          width: 40,
          resizable: false,
          Cell: row => {
            return <input style={ { backgroundColor: 'transparent' } } title={ row.value } value={ row.value } readOnly />
          }
        })

        _.each(newColumns, column => {

          // 添加底部统计
          if (column.statistics) {
            column.Footer = cellInfo => {
              return (
                <div data-id={ 'total-' + cellInfo.column.id }></div>
              )
            }
          }
        })

        this.setState({ columns: newColumns }, resolve)
      })
    }

    createDefaultData() {
      return new Promise(resolve => {
        const data = []

        _.times(5, time => data.push(this.genRows(time + 1)))

        this.setState({ data }, resolve)
      })
    }

    resetOrderNum(data) {
      _.each(data, (item, index) => {
        item.no = index + 1
      })
    }

    handleAddRow() {
      const data = [ ...this.state.data ]
      const len = data.length + 1

      data.push(this.genRows(len))

      this.setState({ data })
    }

    handleDelRow(e) {
      let data = [ ...this.state.data ]
      const index = e.currentTarget.getAttribute('data-index')

      if (data.length > 1) {

        // 重置表单
        this.apiData.splice(index, 1)

        this.resetOrderNum(data)

        data = []

        this.apiData.forEach((item, index) => {
          data[index] = this.genRows(index + 1, item)
        })

        this.setState({ data })
      } else {
        this.showToolTip('至少保留一条数据', 'failed')
      }
    }

    handleCellInput(e, column) {
      const index = e.currentTarget.getAttribute('data-index')
      const id = e.currentTarget.getAttribute('data-api-id')

      // 统计
      this.statistics(id)

      if (column.formateFunc) {

        // 需要格式化的值
        e.currentTarget.value = util[column.formateFunc](e.currentTarget.value)
        this.apiData[index - 1][id] = column.fixed ? Number(e.currentTarget.value).toFixed(2) : Number(e.currentTarget.value)
      } else {
        this.apiData[index - 1][id] = e.currentTarget.value
      }

      // 计算金额
      if (column.operation) {
        const thisValue = Number(e.currentTarget.value)
        let total = 0

        switch (column.operation.type) {
          case 'multiplication':

            column.operation.anothers.forEach(another => {
              const anotherValue = Number(this.apiData[index - 1][another]) || 0

              total = thisValue * anotherValue
            })

            if (total >= 0) {
              this.apiData[index - 1][column.operation.target] = total.toFixed(2)
              this.billPage.querySelectorAll('input[data-api-id="' + column.operation.target + '"]')[index - 1].value = total.toFixed(2)
            } else {
              this.apiData[index - 1][column.operation.target] = ''
              this.billPage.querySelectorAll('input[data-api-id="' + column.operation.target + '"]')[index - 1].value = ''
            }

            break

          case 'minus':
            if (e.currentTarget.value) {
              column.operation.anothers.forEach(another => {
                const anotherValue = Number(this.apiData[index - 1][another]) || 0

                total = thisValue - anotherValue
              })
              this.apiData[index - 1][column.operation.target] = (column.operation.noFixed ? total : total.toFixed(2))
              this.billPage.querySelectorAll('input[data-api-id="' + column.operation.target + '"]')[index - 1].value = this.apiData[index - 1][column.operation.target]
            } else {
              this.apiData[index - 1][column.operation.target] = ''
              this.billPage.querySelectorAll('input[data-api-id="' + column.operation.target + '"]')[index - 1].value = ''
            }
            break
        }

        this.statistics(column.operation.target, thisValue)
      }

    }

    statistics(key) {
      const elements = this.billPage.querySelectorAll('input[data-api-id="' + key + '"]')
      const item = _.find(columns, column => column.accessor === key)
      let total = 0

      elements.forEach(element => {
        total += Number(element.value)
      })

      const element = this.billPage.querySelector('[data-id="total-' + key + '"]')

      if (element) {
        if (total >= 0) {
          element.innerHTML = item.fixed ? total.toFixed(2) : total
        } else {
          element.innerHTML = ''
        }

        if (item.link) {
          this.billPage.querySelector('[data-api-id="' + item.link + '"]').value = element.innerHTML
        }

        if (item.links) {
          item.links.forEach(link => {
            if (link.constructor === String) {
              this.billPage.querySelector('[data-api-id="' + link + '"]').value = element.innerHTML
            } else {
              if (this.billPage.querySelector('[data-api-id="' + link[1] + '"]').value && element.innerHTML) {
                this.billPage.querySelector('[data-api-id="' + link[0] + '"]').value = (element.innerHTML - this.billPage.querySelector('[data-api-id="' + link[1] + '"]').value).toFixed(2)
              } else {
                this.billPage.querySelector('[data-api-id="' + link[0] + '"]').value = Number(element.innerHTML).toFixed(2)
              }
            }
          })
        }
      }

      this.total = total
    }

    handleCellBlur(e, column) {
      if (column.fixed) {
        if (e.currentTarget.value) {
          e.currentTarget.value = parseFloat(e.currentTarget.value).toFixed(2)
        }
      }
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

    showIndicator() {
      this.setState({ indicatorDisplay: true })
    }

    hideIndicator() {
      this.setState({ indicatorDisplay: false })
    }

    render() {
      const Table = genTable({
        pageSize: this.state.pageSize,
        columns: this.state.columns,
        data: this.state.data
      })

      return (
        <div className="crm-bill-page" ref={ billPage => this.billPage = billPage }>

          <Indicator show={ this.state.indicatorDisplay } />

          { this.state.depotModalboxDisplay ? <DepotModalbox transferTrData={ data => this.transferTrData(data) } handleClose={ this.hideDepotModalbox.bind(this) } /> : null }

          { this.state.prodModalboxDisplay ? <ProdModalbox transferTrData={ data => this.transferTrData(data) } handleClose={ this.hideProdModalbox.bind(this) } /> : null }

          { Header && <header><Header pageInstance={ this } /></header> }

          <div className="body">
            { this.state.data.length > 0 ? <Table pageSize={ this.state.data.length } /> : <div style={ { textAlign: 'center' } } >暂无数据</div> }

            { Calculation && <div className="calculation"><Calculation pageInstance={ this } /></div> }

            <Tooltip show={ this.state.tooltipDisplay } text={ this.state.tooltipText } type={ this.state.tooltipType } />

            <footer><Footer pageInstance={ this } /></footer>
          </div>
        </div>
      )
    }
  }
}

export default genBillPage
