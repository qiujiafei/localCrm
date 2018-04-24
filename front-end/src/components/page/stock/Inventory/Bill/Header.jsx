import React, { Component } from 'react'
import PropTypes from 'prop-types'
import InputDateTime from '../../../../widget/form/InputDateTime'
import Button from '../../../../widget/Button'
import InputDepot from '../../../../widget/InputDepot'
import InputProd from '../../../../widget/InputProd'
import DepotModalbox from '../../../../widget/DepotModalbox/DepotModalbox'
import ajax from '../../../../../lib/ajax'
import ProdModalbox from './ProdModalbox/ProdModalbox'
import ProdModalboxInfo from './ProdModalbox/apiInfo'

class Header extends Component {
  static propTypes = {
    pageInstance: PropTypes.object
  }

  constructor(props) {
    super(props)

    this.state = {
      displayModalbox: false,
      displayProdModalbox: false
    }
  }

  componentDidMount() {
    ProdModalboxInfo.data.depot_id = ''
  }

  getData(data) {
    const { pageInstance } = this.props

    pageInstance.showIndicator()

    ajax({
      method: 'GET',
      url: '/inventory/get/allow-commodity.do',
      data
    }).then(info => {
      if (info.err) {
        pageInstance.showToolTip(info.desc, 'failed')

        if (info.goToLogin) {
          setTimeout(() => { location.href = '/login' }, 3000)
        }
      } else {
        pageInstance.originData = info.data.lists
        pageInstance.setState({ data: info.data.lists.map((item, index) => pageInstance.genRows(index + 1, item)) })
      }

      pageInstance.hideIndicator()
    })
  }

  handleArrowClick() {
    this.setState({ displayModalbox: true })
  }

  handleProdArrowClick() {
    this.setState({ displayProdModalbox: true })
  }

  handleClose() {
    this.setState({ displayModalbox: false })
  }

  handleProdBoxClose() {
    this.setState({ displayProdModalbox: false })
  }

  handleSearch() {
    const depotId = this.wrapper.querySelector('[data-api-id="depot_id"]').value
    const commodityBatchId = this.wrapper.querySelector('[data-api-id="commodity_batch_id"]').value

    this.getData({ depot_id: depotId, batch_id: commodityBatchId, pageSize: 999 })
  }

  transferTrData(data) {
    const { original } = data.rowInfo

    this.wrapper.querySelector('[data-api-id="depot_id"]').value = original.id
    this.wrapper.querySelector('[data-id="depot_name"]').value = original.depot_name

    ProdModalboxInfo.data.depot_id = original.id

    this.setState({ displayModalbox: false })
  }

  transferProdTrData(data) {
    const { original } = data.rowInfo

    this.wrapper.querySelector('[data-api-id="commodity_batch_id"]').value = original.commodity_batch_id
    this.wrapper.querySelector('[data-id="commodity_name"]').value = original.commodity_name

    this.setState({ displayProdModalbox: false })
  }

  render() {
    return (
      <div ref={ wrapper => this.wrapper = wrapper }>
        <input data-api-id="depot_id" type="hidden" />
        <input data-api-id="commodity_batch_id" type="hidden" />

        <InputDateTime text="盘点时间" readOnly disabled />
        <InputDepot data-id="depot_name" handleArrowClick={ () => this.handleArrowClick() } />
        <InputProd data-id="commodity_name" handleArrowClick={ () => this.handleProdArrowClick() } />
        <Button text="查询" type="secondary" onClick={ () => this.handleSearch() } />

        { this.state.displayModalbox && <DepotModalbox handleClose={ () => this.handleClose() } transferTrData={ data => this.transferTrData(data) } /> }
        { this.state.displayProdModalbox && <ProdModalbox handleClose={ () => this.handleProdBoxClose() } transferTrData={ data => this.transferProdTrData(data) } /> }
      </div>
    )
  }
}

export default Header
