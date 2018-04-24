/* global _ */

import React, { Component } from 'react'
import PropTypes from 'prop-types'
import rightColumnsForService from './rightColumnsForService'
import rightColumnsForProd from './rightColumnsForProd'
import leftColumns from './leftColumns'
import CustomerModalbox from './CustomerModalbox/CustomerModalbox'
import WorkerModalbox from './WorkerModalbox/WorkerModalbox'
import serviceApi from './service.api'
import prodApi from './prod.api'
import InputText from '../../../../widget/form/InputText'
import InputWorker from '../../../../widget/InputWorker'
import Button from '../../../../widget/Button'
import Tooltip from '../../../../widget/Tooltip/Tooltip'
import genRadioButtonGroup from '../../../../HOC/genRadioButtonGroup'
import genTable from '../../../../HOC/genTable'
import Indicator from '../../../../widget/Indicator/Indicator'
import Pagination from '../../../../widget/Pagination/Pagination'
import ajax from '../../../../../lib/ajax'
import util from '../../../../../lib/util'

import './Order.styl'

const RadioButtonGroup = genRadioButtonGroup({
  name: 'item',
  buttons: [
    {
      id: 'service',
      text: '服务项目'
    },
    {
      id: 'prod',
      text: '库存商品'
    }
  ]
})

class Order extends Component {
  static propTypes = {
    transferData: PropTypes.func,
    mapInfo: PropTypes.object
  }

  constructor(props) {
    super(props)

    this.state = {

      // 提示框
      toolTipText: '',
      toolTipType: '',
      toolTipDisplay: false,

      // 模态框
      customerModalboxDisplay: false,
      workerModalboxDisplay: false,

      // 加载动画
      leftIndicatorDisplay: false,
      rightIndicatorDisplay: false,
      pageIndicatorDisplay: false,

      // 数据
      leftColumns: leftColumns,
      rightColumns: rightColumnsForService,
      leftData: [],
      rightData: [],

      // 分页
      pageSize: 15,
      current: 1,
      totalCount: 0
    }

    this.onCustomerSelect = this.onCustomerSelect.bind(this)
    this.onCustomerModalboxClose = this.onCustomerModalboxClose.bind(this)
    this.onButtonGroupItemClick = this.onButtonGroupItemClick.bind(this)
    this.transferTrData = this.transferTrData.bind(this)
    this.onRightSearch = this.onRightSearch.bind(this)

    this.apiData = {}
    this.leftData = []
  }

  componentDidMount() {
    this.wrapper.querySelector('[data-id="item-service"]').click()
  }

  componentDidUpdate() {
    this.calcTotal()
    this.calcCount()
  }

  onCustomerSelect() {
    this.showCustomerModalbox()
  }

  onCustomerModalboxClose() {
    this.hideCustomerModalbox()
  }

  onButtonGroupItemClick(id) {
    this.wrapper.querySelector('[data-api-id="keyword"]').value = ''
    prodApi.data.page_num = serviceApi.data.page = 1

    switch (id) {
      case 'item-prod':
        this.rightType = 'prod'
        this.setState({ current: 1 }, () => this.getProdData())
        break
      case 'item-service':
        this.rightType = 'service'
        this.setState({ current: 1 }, () => this.getServiceData())
        break

      // 没有 default
    }
  }

  onRightSearch() {
    prodApi.data.page_num = serviceApi.data.page = 1

    if (this.rightType === 'service') {
      serviceApi.data.service_name = this.wrapper.querySelector('[data-api-id="keyword"]').value
      this.setState({ current: 1 }, () => {
        this.getServiceData()
        prodApi.data.keyword = serviceApi.data.service_name = ''
      })
    } else {
      prodApi.data.keyword = this.wrapper.querySelector('[data-api-id="keyword"]').value
      this.setState({ current: 1 }, () => {
        this.getProdData()
        prodApi.data.keyword = serviceApi.data.service_name = ''
      })
    }
  }

  getProdData() {
    this.showRightIndicator()

    ajax({
      method: 'GET',
      url: prodApi.url,
      data: prodApi.data
    }).then(info => {
      if (info.err) {
        this.showTip(info.desc, 'failed')

        if (info.goToLogin) {
          setTimeout(() => {
            location.href = '/login'
          }, 3000)
        }
      } else {
        this.setState({
          totalCount: info.data.total_count,
          rightColumns: rightColumnsForProd,
          rightData: info.data[prodApi.listKey]
        })
      }

      this.hideRightIndicator()
    })
  }

  getServiceData() {
    this.showRightIndicator()

    ajax({
      method: 'GET',
      url: serviceApi.url,
      data: serviceApi.data
    }).then(info => {
      if (info.err) {
        this.showTip(info.desc, 'failed')

        if (info.goToLogin) {
          setTimeout(() => {
            location.href = '/login'
          }, 3000)
        }
      } else {
        this.setState({
          totalCount: info.data.total_count,
          rightColumns: rightColumnsForService,
          rightData: info.data[serviceApi.listKey]
        })
      }

      this.hideRightIndicator()
    })
  }

  showTip(text, type) {
    this.setState({ toolTipDisplay: true, toolTipText: text, toolTipType: type })
    setTimeout(() => { this.hideTip() }, 3000)
  }

  hideTip() {
    this.setState({ toolTipDisplay: false })
  }

  showLeftIndicator() {
    this.setState({ leftIndicatorDisplay: true })
  }

  hideLeftIndicator() {
    this.setState({ leftIndicatorDisplay: false })
  }

  showPageIndicator() {
    this.setState({ pageIndicatorDisplay: true })
  }

  hidePageIndicator() {
    this.setState({ pageIndicatorDisplay: false })
  }

  showRightIndicator() {
    this.setState({ rightIndicatorDisplay: true })
  }

  hideRightIndicator() {
    this.setState({ rightIndicatorDisplay: false })
  }

  showCustomerModalbox() {
    this.setState({ customerModalboxDisplay: true })
  }

  hideCustomerModalbox() {
    this.setState({ customerModalboxDisplay: false })
  }

  showWorkerModalbox() {
    this.setState({ workerModalboxDisplay: true })
  }

  hideWorkerModalbox() {
    this.setState({ workerModalboxDisplay: false })
  }

  transferTrData(data) {
    const { original } = data.rowInfo
    const memberElement = this.wrapper.querySelector('[data-insert-id="member_discount"]')

    for (const cell in original) {
      const element = this.wrapper.querySelector('[data-id="' + cell + '"]')

      if (element) {
        element.value = original[cell]
        element.title = element.value
      }
    }

    // 车牌特殊处理
    this.wrapper.querySelector('[data-id="number"]').title =
    this.wrapper.querySelector('[data-id="number"]').value =
      original.number_plate_province_name + original.number_plate_alphabet_name + original.number_plate_number

    if (original.is_member == 0) {

      // 不是会员，会员优惠不可用
      memberElement.disabled = true

      // 清空会员优惠
      memberElement.value = ''
      this.calcTotal()
    } else {
      memberElement.disabled = false
    }
  }

  transferWorkerTrData(data) {
    const { original } = data.rowInfo

    this.wrapper.querySelector('[data-insert-id="technician_id"]').value = original.id
    this.wrapper.querySelector('[data-id="technician_name"]').value = original.name
  }

  insertLeftData(rowInfo) {
    const { original } = rowInfo
    let data = {}

    // 索引
    data.paramIndex = this.leftData.length

    if (this.rightType !== 'service') {
      data.commodity_batch_id = original.id
      data.name = original.commodity_name
      data.group = original.classification_name
      data.quantity = '1.00'
    } else {
      data.service_id = original.id
      data.name = original.service_name
      data.group = original.service_claasification_name
      data.quantity = 1
    }

    data.count = (
      <input
        data-index={ data.paramIndex }
        data-type={ this.rightType }
        data-name="number"
        type="text"
        onInput={ e => {
          const index = e.currentTarget.getAttribute('data-index')
          const type = e.currentTarget.getAttribute('data-type')
          const priceElement = this.wrapper.querySelectorAll('[data-id="price"]')[index]
          const totalElement = this.wrapper.querySelectorAll('[data-id="cost"]')[index]
          const quantity = type !== 'service' ? util.formateToDecimal(e.currentTarget.value) : util.formateToInteger(e.currentTarget.value)
          const total = (quantity * priceElement.innerHTML).toFixed(2)

          e.currentTarget.value = quantity
          this.leftData[index].quantity = quantity

          totalElement.innerHTML = total
          totalElement.setAttribute('data-value', total)

          this.calcTotal()
        } }
        onBlur={ e => {
          const type = e.currentTarget.getAttribute('data-type')
          e.currentTarget.value = type !== 'service' ? Number(e.currentTarget.value).toFixed(2) : e.currentTarget.value
        } }
        placeholder="请输入数量"
        maxLength="10"
      />
    )

    data['paramType'] = this.rightType
    data.price = original.price
    data.created_time = original.created_time

    // 计算默认金额
    data.cost = (Number(data.price) * 1).toFixed(2)

    // 去重
    const nonProd = !_.find(this.leftData, item => item.commodity_batch_id === original.id)
    const nonService = !_.find(this.leftData, item => item.service_id === original.id)

    if (nonProd && this.rightType === 'prod') {
      this.leftData.push(data)
    }

    if (nonService && this.rightType === 'service') {
      this.leftData.push(data)
    }

    this.setState({ leftData: this.leftData })
  }

  calcTotal() {
    const costElements = this.wrapper.querySelectorAll('[data-id="cost"]')
    const totalCostElement = this.wrapper.querySelector('[data-id="total-cost"]')
    const value = _.reduce(costElements, (accumulator, costElement) => accumulator + (Number(costElement.innerHTML) || 0), 0)
    const discountElement = this.wrapper.querySelector('[data-insert-id="member_discount"]')

    // 商品金额
    const prodCount = this.state.leftData.reduce((accumulator, item) => {
      if (item.paramType === 'prod') {
        accumulator += Number(item.price) * item.quantity
      } else {
        accumulator += 0
      }
      return accumulator
    }, 0)

    totalCostElement.innerHTML = (Number(value) - prodCount - (Number(discountElement.value) || 0)).toFixed(2)
  }

  onSave(type) {

    const elements = this.wrapper.querySelectorAll('[data-insert-id]')

    elements.forEach(element => {
      const id = element.getAttribute('data-insert-id')
      this.apiData[id] = element.value
    })

    let picking_commodity = this.state.leftData.filter(item => item.paramType === 'prod')
    let service_info = this.state.leftData.filter(item => item.paramType === 'service')

    picking_commodity = picking_commodity.map(item => {
      return {
        commodity_batch_id: item.commodity_batch_id,
        quantity: item.quantity
      }
    })

    // service_info = service_info.reduce((accummulate, item, index) => {
    //   accummulate[index] = item.service_id
    //
    //   return accummulate
    // }, [])

    service_info = service_info.map(item => {
      return {
        service_id: item.service_id,
        quantity: item.quantity
      }
    })

    this.apiData.picking_commodity = picking_commodity
    this.apiData.service_info = service_info

    if (!this.apiData.customer_infomation_id) {
      this.showTip('请选择客户', 'failed')
      return
    }

    if (!this.apiData.technician_id) {
      this.showTip('请选择施工人员', 'failed')
      return
    }

    if (this.apiData.service_info.length === 0) {
      this.showTip('请选择服务项目', 'failed')
      return
    }

    this.apiData.status = type

    this.showPageIndicator()

    ajax({
      method: 'POST',
      url: '/bill/put/insert.do',
      data: this.apiData
    }).then(info => {

      if (info.err) {
        this.showTip(info.desc, 'failed')
        if (info.goToLogin) {
          setTimeout(() => location.href = '/login', 3000)
        }
      } else {
         this.showTip(type == 1 ? '结算成功' : '挂单成功', 'success')

         if (type == 1) {
          setTimeout(() => {
            document.querySelector('[data-id="已结算单据"]').click()
          }, 3000)
         } else {
          setTimeout(() => {
            document.querySelector('[data-id="待结算单据"]').click()
          }, 3000)
         }
      }

      this.hidePageIndicator()
    })
  }

  calcCount() {
    this.leftData.forEach((item, index) => {
      const price = this.wrapper.querySelectorAll('[data-id="price"]')[index].getAttribute('data-value')
      const quantity = item.quantity || 1
      const total = (price * quantity).toFixed(2)

      if (parseFloat(item.quantity) >= 0) {
        this.wrapper.querySelectorAll('[data-name="number"]')[index].value = item.paramType !== 'service' ? Number(quantity).toFixed(2) : quantity
        this.wrapper.querySelectorAll('[data-id="cost"]')[index].innerHTML = total
        this.wrapper.querySelectorAll('[data-id="cost"]')[index].setAttribute('data-value', total)
      } else {
        this.wrapper.querySelectorAll('[data-name="number"]')[index].value = ''
        this.wrapper.querySelectorAll('[data-id="cost"]')[index].innerHTML = '0.00'
        this.wrapper.querySelectorAll('[data-id="cost"]')[index].setAttribute('data-value', '0.00')
      }
    })

    this.calcTotal()
  }

  render() {

    // 左边 table 添加操作栏
    const leftColumns = this.state.leftColumns.slice()

    leftColumns.unshift({
      Header: '操作',
      accessor: 'selector',
      width: 40,
      resizable: false,
      Cell: row => {
        return (
          <a
            className="crm-icon crm-icon-delete"
            data-index={ row.index }
            style={ { cursor: 'pointer' } }
            title="删除"
            onClick={ e => {
              const index = e.currentTarget.getAttribute('data-index')

              this.leftData.splice(index, 1)

              this.setState({ leftData: this.leftData })
            } }
          ></a>
        )
      }
    })

    const RightTable = genTable({
      columns: this.state.rightColumns,
      data: this.state.rightData
    })

    const LeftTable = genTable({
      columns: leftColumns,
      data: this.state.leftData
    })

    return (
      <div className="crm-order-page" ref={ wrapper => this.wrapper = wrapper }>

        <Indicator show={ this.state.pageIndicatorDisplay } />

        <Tooltip
          text={ this.state.toolTipText }
          type={ this.state.toolTipType }
          show={ this.state.toolTipDisplay }
        />

        { this.state.customerModalboxDisplay &&
          <CustomerModalbox
            transferTrData={ data => {
              data.type = 0
              this.transferTrData(data)
              this.hideCustomerModalbox()
            } }
            parentPage={ this }
            handleClose={ this.onCustomerModalboxClose }
          />
        }
        { this.state.workerModalboxDisplay &&
          <WorkerModalbox
            parentPage={ this }
            handleClose={ () => this.hideWorkerModalbox() }
            transferTrData={ data => {
              data.type = 1
              this.transferWorkerTrData(data)
              this.hideWorkerModalbox()
            } }
          />
        }

        <div className="left">
          <div className="inner">
            <header>
              <Button text="选择客户" type="link" onClick={ this.onCustomerSelect } />
              <Button text="新增客户" type="link" onClick={ () => this.props.transferData({ tab: '客户资料', panel: this.props.mapInfo['客户资料'].component }) } />
              <table>
                <tbody>
                  <tr>
                    <td>卡号</td>
                    <td>
                      <input data-id="id" data-insert-id="customer_infomation_id" type="hidden" />
                      <input data-id="card_number" type="text" readOnly />
                    </td>
                    <td>姓名</td>
                    <td><input data-id="customer_name" type="text" readOnly /></td>
                    <td>手机</td>
                    <td><input data-id="cellphone_number" type="text" readOnly /></td>
                    <td>品牌车系</td>
                    <td><input data-id="brand_name" type="text" readOnly /></td>
                  </tr>
                  <tr>
                    <td>车架号</td>
                    <td><input data-id="frame_number" type="text" readOnly /></td>
                    <td>车牌</td>
                    <td><input data-id="number" type="text" readOnly /></td>
                    <td>客户来源</td>
                    <td><input data-id="customer_origination" type="text" readOnly /></td>
                  </tr>
                </tbody>
              </table>
            </header>

            <LeftTable
              pageSize={ this.state.leftData.length > 15 ? this.state.leftData.length : 15 }
              getTdProps={
                (state, rowInfo, column) => {
                  return {
                    'data-id': column ? column.id : '',
                    'data-value': rowInfo ? rowInfo.original[column.id] : ''
                  }
                }
              }
            />

            <Indicator show={ this.state.leftIndicatorDisplay } />

            <footer>
              <div className="crm-color crm-color-error">￥: <span data-id="total-cost">0.00</span>（快捷开单不计算商品金额）</div>
              <div>
                <input data-insert-id="technician_id" type="hidden" />
                <InputWorker
                  data-id="technician_name"
                  handleArrowClick={ () => {
                    this.showWorkerModalbox()
                  } }
                />
                <InputText data-insert-id="comment" text="备注" />
                <InputText
                  data-insert-id="member_discount"
                  text="会员优惠"
                  onInput={ e => {
                    e.currentTarget.value = util.formateToDecimal(e.currentTarget.value)
                    this.calcTotal()
                  } }
                  onBlur={ e => e.currentTarget.value = Number(e.currentTarget.value).toFixed(2) }
                  disabled
                />
              </div>
              <div>
                <Button text="结算" type="secondary" onClick={ () => this.onSave(1) } />
                {/* <Button
                  text="刷新"
                  onClick={ () => {
                    this.setState({
                      leftData: [],
                      rightData: []
                    })
                  } }
                /> */}
                {/* <Button text="挂单" type="secondary" onClick={ () => this.onSave(0) } /> */}
              </div>
            </footer>
          </div>
        </div>
        <div className="right">
          <div className="inner">
            <header>
              <InputText data-api-id="keyword" placeholder="服务项目/商品模糊查询" noLabel />
              <Button text="查询" type="secondary" onClick={ this.onRightSearch } />

              <div className="group">
                <RadioButtonGroup onItemClick={ this.onButtonGroupItemClick } />
              </div>
            </header>

            <RightTable
              getTrProps={
                (state, rowInfo) => {
                  return {
                    style: {
                      cursor: 'pointer'
                    },
                    onClick: () => {
                      this.insertLeftData(rowInfo)
                    }
                  }
                }
              }
            />

            <Pagination
              pageSize={ this.state.pageSize }
              current={ this.state.current }
              total={ this.state.totalCount }
              onChange={ current => {
                this.setState({ current })

                if (this.rightType === 'service') {
                  serviceApi.data.page_num = current
                  this.getServiceData()
                } else {
                  prodApi.data.page_num = current
                  this.getProdData()
                }
              } }
            />

            <Indicator show={ this.state.rightIndicatorDisplay } />
          </div>
        </div>
      </div>
    )
  }
}

export default Order
