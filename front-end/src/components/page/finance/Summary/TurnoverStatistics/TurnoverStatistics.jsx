import React, { Component } from 'react'
import Search from './Search'
import genTable from '../../../../HOC/genTable'
import './TurnoverStatistics.styl'
import Columns from './TurnoverStatistics.columns'
import ajax from '../../../../../lib/ajax'
// import Pagination from '../../../../widget/Pagination/Pagination'

class TurnoverStatistics extends Component {
  constructor(props) {
    super(props)

    this.state = {
      total: '0.00',
      data: [],
      totalCount: 0,
      current: 1
    }
  }

  componentDidMount() {
    this.getData({})
  }

  getData({ startDate = '', endDate = '', searchTime = 'month', page = 1 }) {
    ajax({
      method: 'GET',
      url: '/finance/get/service-lists.do',
      data: {
        startDate,
        endDate,
        searchTime,
        page,
        pageSize: -1
      }
    }).then(info => {
      this.setState({
        total: info.data.total_number,
        data: info.data.lists,
        totalCount: Number(info.data.total_count)
      })
    })
  }

  render() {
    const AmountTable = genTable({
      columns: Columns,
      data: this.state.data
    })

    return (
      <div className="crm-basic-page crm-finance-cost-page" ref={ wrapper => this.wrapper = wrapper }>
        <header>
          <Search pageInstance={ this } />
        </header>
        <div className="crm-table-control">
          <div className="crm-table-total">
            总施工次数: <span id="Amount">{ this.state.total }</span>
          </div>
          <AmountTable pageSize={ this.state.totalCount > 15 ? this.state.totalCount : 15 } />
          {/* {
            this.state.data.length > 0 && (
              <Pagination
                pageSize={ 15 }
                current={ this.state.current }
                total={ this.state.totalCount }
                onChange={ current => {
                  this.setState({ current })
                  this.getData({ page: current })
                } }
              />
            )
          } */}
        </div>
      </div>
    )
  }
}

export default TurnoverStatistics