/* global Promise */

import React, { Component } from 'react'
import genTable from './genTable'
import Tooltip from '../widget/Tooltip/Tooltip'
import Indicator from '../widget/Indicator/Indicator'
import Confirm from '../widget/Confirm/Confirm'
import Pagination from '../widget/Pagination/Pagination'
import Checkbox from '../widget/Checkbox/Checkbox'
import Button from '../widget/Button'
import Alert from '../widget/Alert/Alert'
import ajax from '../../lib/ajax'
import util from '../../lib/util'

function genBasicPage(options) {
  let {
    // 是否显示批量操作（默认 true）
    displayBatchBtn = true,
    // 是否显示单行操作（默认 true）
    displayLineBtn = true,
    // 是否显示序号字段（默认 true）
    displayOrderNum = true,
    // 是否显示分页（默认 true）
    displayPagination = true,
    // 是否显示合计（默认 false）
    displayTotal = false,
    // 单行操作按钮
    lineBtns = [
      {
        id: 'editLine',
        name: '修改',
        className: 'link'
      },
      {
        id: 'delLine',
        name: '删除',
        className: 'link-gray'
      }
    ],
    // 表格字段
    columns = [],
    // 菜单
    menus = [],
    // api 信息
    apiInfo = {},
    // 其他表格配置项
    genTableConfig = () => {},
    // 模态框
    Modalbox = null,
    // 非控制模态框
    UnControlledModalbox = null,
    // 搜索表单
    Search = null,
    // 模态框点击确定
    onModalboxSubmitClick
  } = options

  // 添加刷新按钮
  menus = menus.slice()
  menus[options.menus.length] = {
    id: 'refresh',
    name: '刷新'
  }

  // const originSearchData = _.cloneDeep(apiInfo.data)

  return class BasicPage extends Component {
    constructor(props) {
      super(props)

      this.state={

        // 非控制模态框
        uncontrolledModalboxDisplay: false,

        // 模态框
        modalBoxDisplay: false,
        modalBoxType: '新增',
        modalBoxIndicator: false,

        // 提示框
        toolTipText: '成功',
        toolTipType: 'normal',
        toolTipDisplay: false,

        // 对话框
        alertDisplay: false,
        alertMsg: '',
        alertContent: '',

        // 加载动画
        indicatorDisplay: false,

        // 当前组件
        columns: columns,
        data: [],
        totalCount: 0,
        pageSize: apiInfo.data.data.pageSize || apiInfo.data.data.count_per_page,
        current: 1
      }
    }

    componentDidMount() {
      this.init()
    }

    componentDidUpdate() {
      this.sum()
    }

    async init() {
      await this.initColumns()
      await this.getData(1)
    }

    sum() {
      const sumElements = this.page.querySelectorAll('[data-sum-for]')

      sumElements.forEach(sumElement => {
        const id = sumElement.getAttribute('data-sum-for')
        const groupElements = this.page.querySelectorAll('[data-id="' + id + '"]')
        let total = 0

        groupElements.forEach(groupElement => {
          total += parseFloat(groupElement.innerHTML)
        })

        sumElement.innerHTML = total > 0 ? total.toFixed(2) : ''
      })
    }

    initColumns() {
      const newColumns = columns.slice()

      if (displayTotal) {
        newColumns[0].Footer = <div>合计：</div>
      }

      if (displayOrderNum) {
        newColumns.unshift({
          Header: '序号',
          accessor: 'no',
          width: 60
        })
      }

      if (displayBatchBtn) {
        newColumns.unshift({
          Header: <Checkbox onChange={ e => { this.setCheckboxStatus(e.currentTarget.checked) } } />,
          accessor: 'selector',
          width: 40,
          resizable: false
        })
      }

      if (displayLineBtn) {
        newColumns.push({
          Header: '操作',
          accessor: 'interaction',
          resizable: false
        })
      }

      // 字段显示 title
      newColumns.forEach(column => {
        if (!column.Cell) {
          column.Cell = function Cell(row) {
            if (typeof row.value !== 'object') {
              return <div title={ row.value } data-id={ column.accessor }>{ row.value.toString().replace(' ', '　') }</div>
            } else {
              return <div>{ row.value }</div>
            }
          }
        }

        if (column.displaySum) {
          column.Footer = <div data-sum-for={ column.accessor }></div>
        }
      })

      return new Promise(resolve => {
        this.setState({ columns: newColumns }, resolve)
      })
    }

    async ajaxRequest(options) {
      this.showIndicator()

      const {
        method = 'GET',
        url,
        data,
        beforeRequest = () => {},
        afterRequest = () => {}
      } = options

      beforeRequest()

      const result = await ajax({
        method,
        url,
        data
      }).then(info => {
        if (info.err) {
          this.handelApiError(info)
          return null
        } else {
          return info
        }
      })

      afterRequest(result)

      this.hideIndicator()
    }

    getData(curPage) {
      const { url, data } = apiInfo.data

      this.setCheckboxStatus(false)
      this.showIndicator()

      this.ajaxRequest({
        url,
        data,
        beforeRequest: () => {
          if (displayPagination) {
            if (data.page_num) {
              data.page_num = curPage
            }
            if (data.page) {
              data.page = curPage
            }

            this.setState({ current: curPage })
          }
        },
        afterRequest: info => {
          if (info) {
            this.setState({
              totalCount: parseFloat(info.data.total_count),
              data: this.formatData(info.data)
            }, () => {
              setTimeout(() => {
                this.sum()
              }, 0)
            })
          }
        }
      })
    }

    add() {
      const { url, data, alert } = apiInfo.insertion

      this.showModalBoxIndicator()

      ajax({
        method: 'POST',
        url,
        data
      })
        .then(info => {
          if (info.err) {
            this.handelApiError(info)
          } else {
            this.getData(1)
            this.hideModalBox()

            if (alert) {
              if (alert.contentFromApiKey) {
                this.showAlert(alert.msg, info.data[alert.contentFromApiKey])
              } else {
                this.showAlert(alert.msg, alert.content)
              }
            }
          }

          this.hideModalBoxIndicator()
        })
    }

    modify() {
      const { url, data } = apiInfo.modification

      this.showModalBoxIndicator()

      ajax({
        method: 'POST',
        url,
        data
      })
        .then(info => {
          if (info.err) {
            this.handelApiError(info)
          } else {
            this.getData(this.state.current)
            this.hideModalBox()
          }

          this.hideModalBoxIndicator()
        })
    }

    del() {
      const { url, data } = apiInfo.deletion

      this.hideConfirm()
      this.showIndicator()

      ajax({
        method: 'POST',
        url,
        data
      })
        .then(info => {
          if (info.err) {
            this.handelApiError(info)
          } else {
            this.getData(1)
          }

          this.hideIndicator()

          // 清空
          for (const key in data) {
            apiInfo.deletion.data[key] = []
          }
        })
    }

    enable() {
      const { url, data } = apiInfo.enable

      this.hideConfirm()
      this.showIndicator()

      ajax({
        method: 'POST',
        url,
        data
      })
        .then(info => {
          if (info.err) {
            this.handelApiError(info)
          } else {
            this.getData(1)
          }

          this.hideIndicator()
        })
    }

    disable() {
      const { url, data } = apiInfo.disable

      this.hideConfirm()
      this.showIndicator()

      ajax({
        method: 'POST',
        url,
        data
      })
        .then(info => {
          if (info.err) {
            this.handelApiError(info)
          } else {
            this.getData(1)
          }

          this.hideIndicator()
        })
    }

    export() {
      const { url } = apiInfo.export

      this.showIndicator()

      ajax({
        method: 'GET',
        url
      })
        .then(info => {
          if (info.err) {
            this.handelApiError(info)
          }

          this.hideIndicator()
        })
        .catch(() => {
          location.href = url + '?token=' + localStorage.getItem('9DAYE_CRM_TOKEN')
          this.hideIndicator()
        })
    }

    deprecation() {
      const { url, data } = apiInfo.deprecation

      this.showIndicator()
      this.hideConfirm()

      ajax({
        method: 'POST',
        url,
        data
      })
        .then(info => {
          if (info.err) {
            this.handelApiError(info)
          }

          this.hideIndicator()
        })
    }

    refresh() {
      // 清理搜索内容
      const elements = this.page.querySelectorAll('.search-wrapper input')

      elements.forEach(element => {
        element.value = ''
        element.removeAttribute('data-key')
      })

      this.getData(1)
    }

    count() {
      const { url, data } = apiInfo.count

      this.hideConfirm()
      this.showIndicator()

      ajax({
        method: 'POST',
        url,
        data
      })
        .then(info => {
          if (info.err) {
            this.handelApiError(info)
          } else {
            this.getData(1)
          }

          this.hideIndicator()
        })
    }

    editLine(e, url, callback) {
      const { convertArrayLikeObjToArray } = util
      const id = e.currentTarget.getAttribute('data-id')

      this.showModalBox(e.currentTarget.title, () => {

        if (url) {
          this.showModalBoxIndicator()
          ajax({
            method: 'GET',
            url,
            data: {
              id: id
            }
          }).then(info => {
            if (info.err) {
              this.handelApiError(info)
            }

            callback(info.data, this)

            init.call(this)

            this.hideModalBoxIndicator()
          })
        } else {
          init.call(this)
        }

        function init() {
          const formElements = convertArrayLikeObjToArray(this.modalbox.modalbox.querySelectorAll('input, textarea'))

          this.originData = Object.assign(this.originData || {}, this.state.data.find(item => item[apiInfo.data.manipulationKey] == id))

          formElements.forEach(formElement => {
            const id = formElement.getAttribute('data-id')
            let value = this.originData[id]

            switch (id) {
              case 'employee_id':
                value = this.originData['name']
                formElement.setAttribute('data-key', this.originData[id])
                break
              case 'gender':
                if (this.originData['gender'] == 1) {
                  this.modalbox.modalbox.querySelector('[data-value="1"]').parentNode.className="crm-radio selected"
                  this.modalbox.modalbox.querySelector('[data-value="0"]').parentNode.className="crm-radio unselected"
                } else {
                  this.modalbox.modalbox.querySelector('[data-value="1"]').parentNode.className="crm-radio unselected"
                  this.modalbox.modalbox.querySelector('[data-value="0"]').parentNode.className="crm-radio selected"
                }
                break

              case 'passwd':
              case 'verify_passwd':
                value = ''
                break

              case 'model_id':
                value = this.originData['style_name']
                formElement.setAttribute('data-key', this.originData[id])
                break

              case 'tire_brand_id':
                this.modalbox.modalbox.querySelector('[data-id="tire_brand_name"]').value = this.originData['brand_name']
                formElement.setAttribute('data-key', this.originData[id])
                break

              case 'number_plate_alphabet_id':
                value = this.originData['number_plate_alphabet_name']
                formElement.setAttribute('data-key', this.originData[id])
                break

              case 'number_plate_province_id':
                value = this.originData['number_plate_province_name']
                formElement.setAttribute('data-key', this.originData[id])
                break

              case 'ID_card_image':
              case 'license_image_name':
              case 'vehicle_license_image_name':
              case 'other_picture_name':
                if (this.originData[id]) {
                  formElement.setAttribute('data-img', this.originData[id])
                  formElement.parentNode.nextSibling.setAttribute('data-img', this.originData[id])
                  formElement.parentNode.nextSibling.style.display = 'inline-block'
                }
                break

              default:
                value = this.originData[id]
                break
            }

            // checkbox
            if (formElement.type === 'checkbox') {
              if (this.originData[id] == '0') {
                formElement.checked = false
                formElement.parentNode.className = 'checkbox checkbox-unselected'
              } else {
                formElement.checked = true
                formElement.parentNode.className = 'checkbox checkbox-selected'
              }

              formElement.value = this.originData[id]
            } else if (formElement.type !== 'file') {
                formElement.value = value || ''
            }
          })
        }
      })
    }

    delLine(e) {
      const id = e.currentTarget.getAttribute('data-id')
      let param = [ id ]

      apiInfo.deletion.data[apiInfo.deletion.key] = param

      this.showConfirm('是否确认删除？')
    }

    formatData(originData) {
      const { listKey, manipulationKey } = apiInfo.data
      let result = null

      if (Array.isArray(originData)) {
        if (originData.length > 0) {
          result = originData
        } else {
          result = []
        }
      } else {
        result = originData[listKey]
      }

      result.forEach((item, index) => {
        const id = item[manipulationKey]

        for (const key in item) {
          const thisItem = item[key]

          if (Array.isArray(thisItem)) {
            for (const name in thisItem[0]) {
              item[key + '-' + '0' + '-' + name] = thisItem[0][name]
            }
          }
        }

        if (displayOrderNum) {
          item.no = ((index + 1) + (this.state.current - 1) * this.state.pageSize).toString()
        }

        if (displayBatchBtn) {
          item.selector = <Checkbox data-id={ id } />
        }

        if (displayLineBtn && !item.hideLineBtn) {
          item.interaction = (
            <div>
              {
                lineBtns.map((lineBtn, index) => {
                  return (
                    <span key={ index }>
                      <Button
                        text={ lineBtn.name }
                        type={ lineBtn.className }
                        size="small"
                        data-id={ id }
                        onClick={ e => {
                          e.stopPropagation()

                          // 使用 sessionStorage 暂存 ID
                          sessionStorage.setItem('EDITID', id)
                          this[lineBtn.id](e, lineBtn.url, lineBtn.callback)
                        } }
                      />
                      { index < lineBtns.length - 1 && <span>|</span> }
                    </span>
                  )
                })
              }
            </div>
          )
        }
      })

      return result
    }

    handelApiError(info) {
      this.showTip(info.desc, 'failed')

      if (info.goToLogin) {
        setTimeout(() => {
          location.href = '/login'
        }, 3000)
      }
    }

    handleMenuClick(e, menu) {
      e.preventDefault()

      const { convertArrayLikeObjToArray, clearFormElementsValue, getItemsWillDelete } = util
      const checkboxes = this.page.querySelectorAll('.rt-tbody input[type="checkbox"]:checked')
      const name = e.currentTarget.querySelector('span').innerHTML
      let ids = getItemsWillDelete(checkboxes, 'data-id')

      switch (menu.id) {
        case 'add':

          this.showModalBox('新增', () => {
            const formElements = convertArrayLikeObjToArray(this.modalbox.modalbox.querySelectorAll('input, textarea'))
            clearFormElementsValue(formElements)
          })

          menu.onClick && menu.onClick()

          break

        case 'delete':
          switch (apiInfo.deletion.key) {
            case 'commodity':
              ids = []

              checkboxes.forEach(checkbox => {
                const id = checkbox.getAttribute('data-id')

                ids.push({
                  commodity: this.state.data.find(item => item.barcode === id).commodity_name,
                  barcode: id
                })
              })
              break

            // 没有 default
          }

          if (ids.length === 0) {
            this.showTip('请至少选择一项')
          } else {
            apiInfo.deletion.data[apiInfo.deletion.key] = ids
            this.showConfirm('是否确认删除？', 'del')
          }
          break

        case 'export':
          this.export()
          break

        case 'enable':
          if (ids.length === 0) {
            this.showTip('请至少选择一项')
          } else {
            apiInfo.enable.data[apiInfo.enable.key] = ids
            this.showConfirm('是否确认' + name + '？', 'enable')
          }
          break

        case 'disable':
          if (ids.length === 0) {
            this.showTip('请至少选择一项')
          } else {
            apiInfo.disable.data[apiInfo.disable.key] = ids
            this.showConfirm('是否确认' + name + '？', 'disable')
          }
          break

        case 'deprecation':

          switch (apiInfo.deprecation.type) {
            case 'object':
              ids = []

              checkboxes.forEach(checkbox => {
                const id = checkbox.getAttribute('data-id')

                ids.push({
                  id,
                  comment: ''
                })
              })
              break

            // 没有 default
          }

          if (ids.length === 0) {
            this.showTip('请至少选择一项')
          } else {
            apiInfo.deprecation.data[apiInfo.deprecation.key] = ids
            this.showConfirm('是否确认作废？', 'deprecation')
          }

          break

        case 'refresh':
          this.refresh()
          break

        case 'settlement':
          if (ids.length === 0) {
            this.showTip('请至少选择一项')
          } else {
            apiInfo.count.data[apiInfo.count.key] = ids
            this.showConfirm('是否确认' + name + '？', 'count')
          }
          break

        default:
          menu.onClick(this, this.modalbox, apiInfo)
          break
      }

    }

    handleModalboxSubmit() {
      let pass = true

      const { convertArrayLikeObjToArray } = util

      const data = {}
      const must = []

      const formElements = convertArrayLikeObjToArray(this.modalbox.modalbox.querySelectorAll('input, textarea'))

      formElements.forEach(element => {
        const id = element.getAttribute('data-id')
        const name = element.getAttribute('data-name')

        data[id] = element.value

        switch (id) {
          case 'employee_id':
          case 'number_plate_province_id':
          case 'number_plate_alphabet_id':
          case 'model_id':
            data[id] = element.getAttribute('data-key')
            break

          case 'ID_card_image':
          case 'license_image_name':
          case 'vehicle_license_image_name':
          case 'other_picture_name':
            data[id] = element.getAttribute('data-name')
            break

          case 'gender':
            data[id] = element.parentNode.parentNode.querySelector('.selected > input').getAttribute('data-value')
            break

          case 'is_member':
            data[id] = element.checked ? 1: 0
            break
          // 没有 default
        }

        if (element.hasAttribute('required')) {
          must.push({ id, name })
        }
      })

      for (let index = 0; index < must.length; index += 1) {
        const item = must[index]

        if (!data[item.id]) {
          pass = false
          this.showTip('请输入' + item.name, 'failed')

          break
        }
      }

      if (onModalboxSubmitClick) {
        if (!onModalboxSubmitClick(data, this)) {
          pass = false
        }
      }

      if (pass) {
        if (this.modalbox.state.type === '新增') {
          apiInfo.insertion.data = onModalboxSubmitClick ? onModalboxSubmitClick(data, this) : data

          this.setState({
            current: 1
          }, () => {
            this.add()
          })
        } else {

          for (const key in apiInfo.modification.keyMap) {
            const value = apiInfo.modification.keyMap[key]

            data[key] = this.originData[value]
          }
          apiInfo.modification.data = onModalboxSubmitClick ? onModalboxSubmitClick(data, this) : data
          this.modify()
        }
      }
    }

    setCheckboxStatus(checked) {
      const checkboxes = this.page.querySelectorAll('.rt-table input[type=checkbox]')

      checkboxes.forEach(checkbox => {
        checkbox.checked = checked

        if (checked) {
          checkbox.parentNode.className = 'checkbox checkbox-selected'
        } else {
          checkbox.parentNode.className = 'checkbox checkbox-unselected'
        }
      })
    }

    showIndicator() {
      this.setState({ indicatorDisplay: true })
    }

    hideIndicator() {
      this.setState({ indicatorDisplay: false })
    }

    showTip(text, type) {
      this.setState({ toolTipDisplay: true, toolTipText: text, toolTipType: type })
      setTimeout(() => { this.hideTip() }, 3000)
    }

    hideTip() {
      this.setState({ toolTipDisplay: false })
    }

    showConfirm(msg, type) {
      this.setState({ confirmDisplay: true, confirmMsg: msg, confirmType: type || 'del' })
    }

    hideConfirm() {
      this.setState({ confirmDisplay: false })
    }

    showModalBox(type, callback) {
      this.setState({ modalBoxDisplay: true, modalBoxType: type }, callback)
    }

    hideModalBox() {
      this.setState({ modalBoxDisplay: false })
    }

    showUncontrolledModalbox() {
      this.setState({ uncontrolledModalboxDisplay: true })
    }

    hideUncontrolledModalbox() {
      this.setState({ uncontrolledModalboxDisplay: false })
    }

    showPreviewModalbox() {
      this.setState({ previewModalboxDisplay: true })
    }

    hidePreviewModalbox() {
      this.setState({ previewModalboxDisplay: false })
    }

    showModalBoxIndicator() {
      this.setState({ modalBoxIndicator: true })
    }

    hideModalBoxIndicator() {
      this.setState({ modalBoxIndicator: false })
    }

    showAlert(msg, content) {
      this.setState({ alertDisplay: true, alertMsg: msg, alertContent: content })
    }

    hideAlert() {
      this.setState({ alertDisplay: false })
    }

    render() {
      const Table = genTable({
        pageSize: this.state.pageSize,
        columns: this.state.columns,
        data: this.state.data
      })

      return (
        <div className="crm-basic-page" ref={ page => this.page = page }>

          <header>
            <nav>
              { menus.map((menu, index) => {
                return (
                  <a className="crm-menu-item" onClick={ e => this.handleMenuClick(e, menu) } key={ index }>
                    <i className={ 'crm-icon crm-icon-' + (menu.icon || menu.id) }></i>
                    <span>{ menu.name }</span>
                  </a>
                )
              }) }
            </nav>
            {
              Search && <Search pageInstance={ this } apiInfo={ apiInfo } />
            }
          </header>

          <div className="body">

            <Table
              { ...genTableConfig(this, apiInfo) }
            />

            {
              displayPagination && this.state.data.length > 0 && (
                <Pagination
                  pageSize={ this.state.pageSize }
                  current={ this.state.current }
                  total={ this.state.totalCount }
                  onChange={ current => {
                    this.setState({ current })
                    this.getData(current)
                  } }
                />
              )
            }
          </div>

          <Indicator
            show={ this.state.indicatorDisplay }
          />

          <Tooltip
            text={ this.state.toolTipText }
            type={ this.state.toolTipType }
            show={ this.state.toolTipDisplay }
          />

          <Alert
            show={ this.state.alertDisplay }
            msg={ this.state.alertMsg }
            content={ this.state.alertContent }
            onSubmitClick={ () => { this.hideAlert() } }
          />

          <Confirm
            type={ this.state.confirmType }
            msg={ this.state.confirmMsg }
            show={ this.state.confirmDisplay }
            onCancelClick={ () => this.hideConfirm() }
            onSubmitClick={ type => { this[type]() } }
          />

          {
            Modalbox && (
              <Modalbox
                pageInstance={ this }
                ref={ modalbox => this.modalbox = modalbox }
                type={ this.state.modalBoxType }
                show={ this.state.modalBoxDisplay }
                displayIndicator={ this.state.modalBoxIndicator }
                handleCancelClick={ () => this.hideModalBox() }
                handleCloseClick={ () => this.hideModalBox() }
                handleSubmitClick={ () => this.handleModalboxSubmit() }
              />
            )
          }

          {
            this.state.uncontrolledModalboxDisplay && (
              <UnControlledModalbox pageInstance={ this } />
            )
          }
        </div>
      )
    }
  }
}

export default genBasicPage
