/* global _ */

import React, { Component } from 'react'
import ReactTable, { ReactTableDefaults } from 'react-table'
import listColumns from './listColumns'
import rightColumns from './rightColumns'
import Pagination from '../../../../widget/Pagination/Pagination'
import Tooltip from '../../../../widget/Tooltip/Tooltip'
import Indicator from '../../../../widget/Indicator/Indicator'
import Radio from '../../../../widget/Radio'
import Checkbox from '../../../../widget/Checkbox/Checkbox'
import Button from '../../../../widget/Button'
import ajax from '../../../../../lib/ajax'

import './Department.styl'

class Department extends Component {

  constructor(props) {
    super(props)

    this.state = {

      // 提示框
      toolTipText: '成功',
      toolTipType: 'normal',
      toolTipDisplay: false,

      // 加载动画
      indicatorDisplay: false,
      rightIndicatorDisplay: false,

      // 数据表
      listColumns,
      listData: [],
      rightColumns,
      rightData: [],

      // 分页
      pageSize: 15,
      totalCount: 0,
      current: 1,

    }
  }

  componentDidMount() {
    this.getListData()
  }

  getListData() {
    this.showIndicator()

    ajax({
      method: 'GET',
      url: '/employeeuser/get/getpart.do',
      data: {
        count_per_page: 15,
        page_num: this.state.current
      }
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
          listData: this.formateListData(info.data.employeeuser),
          totalCount: info.data.total_count
        })
      }

      this.resetRadioBtn()
      this.hideIndicator()
    })
  }

  getRightData() {
    this.showRightIndicator()

    this.setState({
      rightData: []
    })

    ajax({
      method: 'GET',
      url: '/authorization/resource/getall.do',
      data: {
        user_id: this.userId
      }
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
          rightData: this.formateRightData(info.data)
        }, () => {
          // this.checkAll()
        })
      }

      this.hideRightIndicator()
    })
  }

  onSave() {
    const { allow, forbidden } = this.getSaveData()

    this.showRightIndicator()

    ajax({
      method: 'POST',
      url: '/authorization/resource/modify.do',
      data: {
        allow: allow,
        fobiden: forbidden,
        user_id: this.userId
      }
    }).then(info => {
      if (info.err) {
        this.showTip(info.desc, 'failed')

        if (info.goToLogin) {
          setTimeout(() => {
            location.href = '/login'
          }, 3000)
        }
      } else {
        this.showTip('权限修改成功', 'success')
      }

      this.hideRightIndicator()
    })
  }

  getSaveData() {
    const checkboxes = this.rightWrapper.querySelectorAll('input[data-api-id]')
    let forbidden = []
    let allow = []

    _.each(checkboxes, checkbox => {
      const id = checkbox.getAttribute('data-api-id').split('-')

      if (checkbox.checked) {
        allow = allow.concat(id)
      } else {
        forbidden = forbidden.concat(id)
      }
    })

    return { allow, forbidden }
  }

  checkAll() {
    const chkAllCheckboxes = this.rightWrapper.querySelectorAll('input[data-for]')

    _.each(chkAllCheckboxes, chkAllCheckbox => {
      const id = chkAllCheckbox.getAttribute('data-for')
      const checkboxes = this.rightWrapper.querySelectorAll('[data-id="' + id + '"]')

      let selectedLength = 0

      _.each(checkboxes, checkbox => {
        if (checkbox.checked) {
          selectedLength += 1
        }
      })

      if (selectedLength === checkboxes.length) {
        chkAllCheckbox.checked = true
        chkAllCheckbox.parentNode.className = 'checkbox checkbox-selected'
      }
    })
  }

  resetRadioBtn() {
    const selectedRadioBtn = this.leftWrapper.querySelector('input[type="radio"]:checked')

    if (selectedRadioBtn) {
      selectedRadioBtn.checked = false
      selectedRadioBtn.parentNode.className = 'crm-radio unselected'
    }
  }

  showTip(text, type) {
    this.setState({ toolTipDisplay: true, toolTipText: text, toolTipType: type })
    setTimeout(() => { this.hideTip() }, 3000)
  }

  hideTip() {
    this.setState({ toolTipDisplay: false })
  }

  showIndicator() {
    this.setState({ indicatorDisplay: true })
  }

  hideIndicator() {
    this.setState({ indicatorDisplay: false })
  }

  showRightIndicator() {
    this.setState({ rightIndicatorDisplay: true })
  }

  hideRightIndicator() {
    this.setState({ rightIndicatorDisplay: false })
  }

  formateListData(data) {
    const result = [ ...data ]

    _.each(result, item => {
      item.selector = <Radio name="people" />
    })

    return result
  }

  formateRightData(data) {
    const resultData = []

    _.each(data, (item, name) => {
      const allTrue = item.reduce((accumulate, current) => {
        if (current.is_valid) {
          accumulate += 1
        }
        return accumulate
      }, 0)

      resultData.push({
        selector: allTrue ? (
              <Checkbox
                defaultChecked={ true }
                data-for={ name }
                data-api-id={
                  item.map((subItem) => {
                    return subItem.id
                  }).join('-')
                }
                // onChange={ e => {
                //   const checkbox = e.currentTarget
                //   const link = checkbox.getAttribute('data-for')
                //   const linkedCheckboxes = document.querySelectorAll('[data-id="' + link + '"]')
                //
                //   linkedCheckboxes.forEach(linkedCheckbox => {
                //     linkedCheckbox.checked = checkbox.checked
                //
                //     if (checkbox.checked) {
                //       linkedCheckbox.parentNode.className = 'checkbox checkbox-selected'
                //     } else {
                //       linkedCheckbox.parentNode.className = 'checkbox checkbox-unselected'
                //     }
                //   })
                // } }
              />
          ) : (
              <Checkbox
                data-for={ name }
                data-api-id={
                  item.map((subItem) => {
                    return subItem.id
                  }).join('-')
                }
                // onChange={ e => {
                //   const checkbox = e.currentTarget
                //   const link = checkbox.getAttribute('data-for')
                //   const linkedCheckboxes = document.querySelectorAll('[data-id="' + link + '"]')
                //
                //   linkedCheckboxes.forEach(linkedCheckbox => {
                //     linkedCheckbox.checked = checkbox.checked
                //
                //     if (checkbox.checked) {
                //       linkedCheckbox.parentNode.className = 'checkbox checkbox-selected'
                //     } else {
                //       linkedCheckbox.parentNode.className = 'checkbox checkbox-unselected'
                //     }
                //   })
                // } }
              />
          ),
        content: (
          <div className="right-item">
            <h3>{ name }</h3>
            {/* <ul>
              { item.map((subItem, index) => {
                return (
                  <li key={ index }>
                    <label>
                      <Checkbox
                        defaultChecked={ subItem.is_valid }
                        data-api-id={ subItem.id }
                        data-id={ name }
                        onChange={ e => {
                          const checkbox = e.currentTarget
                          const link = checkbox.getAttribute('data-id')
                          const linkedCheckboxes = this.rightWrapper.querySelectorAll('[data-id="' + link + '"]')
                          const checkboxAll = this.rightWrapper.querySelector('[data-for="' + link + '"]')
                          let checkedLen = 0

                          linkedCheckboxes.forEach(linkedCheckbox => {
                            if (linkedCheckbox.checked) {
                              checkedLen += 1
                            }
                          })

                          if (checkedLen === linkedCheckboxes.length) {
                            checkboxAll.checked = true
                            checkboxAll.parentNode.className = 'checkbox checkbox-selected'
                          } else {
                            checkboxAll.checked = false
                            checkboxAll.parentNode.className = 'checkbox checkbox-unselected'
                          }
                        } }
                      />

                      { subItem.name }
                    </label>
                  </li>
                )
              }) }
            </ul> */}
          </div>
        )
      })
    })

    return resultData
  }

  render() {
    return (
      <div className="crm-department-page">

        <Tooltip
          text={ this.state.toolTipText }
          type={ this.state.toolTipType }
          show={ this.state.toolTipDisplay }
        />

        <div className="list">
          <div ref={ leftWrapper => this.leftWrapper = leftWrapper }>
            <Indicator
              show={ this.state.indicatorDisplay }
            />

            <ReactTable
              ref={ rtTable => this.rtTable = rtTable }
              showPagination={ false }
              noDataText={ '暂无数据' }
              className="crm-table -striped -highlight"
              defaultPageSize={ this.state.pageSize }
              sortable={ false }
              column={
                Object.assign({}, ReactTableDefaults.column, {
                  headerClassName: 'crm-table-header',
                  className: 'crm-table-cell'
                })
              }
              data={ this.state.listData }
              columns={ this.state.listColumns }
              getTrProps={ (state, rowInfo) => {
                return {
                  style: {
                    cursor: 'pointer'
                  },
                  onClick: e => {
                    const target = e.currentTarget
                    const radioBtn = target.querySelector('.crm-radio > input')

                    this.userId = rowInfo.original.id

                    this.getRightData()

                    if (this.prevRadioBtn) {
                      this.prevRadioBtn.checked = false
                      this.prevRadioBtn.parentNode.className = 'crm-radio unselected'
                    }

                    radioBtn.checked = true
                    radioBtn.parentNode.className = 'crm-radio selected'

                    this.prevRadioBtn = radioBtn
                  }
                }
              } }
            />

            {
              this.state.listData.length > 0 && (
                <Pagination
                  pageSize={ this.state.pageSize }
                  current={ this.state.current }
                  total={ this.state.totalCount }
                  onChange={ current => {
                    this.setState({ current }, () => { this.getListData(current) })
                  } }
                />
              )
            }
          </div>
        </div>
        <div className="right">
          <div ref={ rightWrapper => this.rightWrapper = rightWrapper }>
            <Indicator
              show={ this.state.rightIndicatorDisplay }
            />

            <header>权限设置</header>
            <ReactTable
              ref={ rtTable => this.rtTable = rtTable }
              TheadComponent={ () => null }
              showPagination={ false }
              noDataText={ '暂无数据' }
              className="crm-table -striped -highlight"
              pageSize={ this.state.rightData.length || 5 }
              sortable={ false }
              column={
                Object.assign({}, ReactTableDefaults.column, {
                  headerClassName: 'crm-table-header',
                  className: 'crm-table-cell'
                })
              }
              data={ this.state.rightData }
              columns={ this.state.rightColumns }
            />
            <footer>
              <Button type="secondary" text="保存" onClick={ e => this.onSave(e) } />
            </footer>
          </div>
        </div>
      </div>
    )
  }
}

export default Department
