import React, { Component } from 'react'
import PropTypes from 'prop-types'
import ajax from '../../../lib/ajax'

class GroupTreeDropdown extends Component {
  static propTypes = {
    pageInstance: PropTypes.object
  }

  constructor(props) {
    super(props)

    const { pageInstance, ...rest } = props

    this.state = {
      data: [],
      pageInstance,
      rest
    }

    this.showList = this.showList.bind(this)
    this.hideList = this.hideList.bind(this)
  }

  componentDidMount() {
    window.addEventListener('click', this.hideList)
  }

  componentWillUnmount() {
    window.removeEventListener('click', this.hideList)
  }

  getData() {
    ajax({
      method: 'GET',
      url: '/classification/get/getall.do'
    })
      .then(info => {
        if (!info.err) {
          const data = this.formatData(info.data)

          this.setState({ data })
        } else {
          if (info.goToLogin) {
            this.state.pageInstance.showTip('登录超时', 'failed')
            setTimeout(() => {
              location.href = '/login'
            }, 2500)
          }
        }
      })
  }

  formatData(data) {
    const result = []

    // 每条数据中插入 children
    for (let i = 0; i < data.length; i += 1) {
      const item = data[i]

      if (item.depth === '1') {
        result.push(item)
      }

      if (item.parent_id !== '-1' && item.depth !== '1') {
        const originItem = data.find(dataItem => {
          return dataItem.id === item.parent_id
        })

        if (!originItem.children) {
          originItem.children = []
        }

        originItem.children.push(item)
      }
    }

    return result
  }

  generateTree(data) {
    return (
      <ul>
        { data.map(item => {
          return (
            <li data-key={ item.id } data-value={ item.classification_name } key={ item.id } onClick={ e => this.onItemClick(e) }>
              <div className="item">
                <i className={ item.children && item.children.length > 0 ? 'crm-icon crm-icon-category' : 'crm-icon crm-icon-file' } onClick={ e => this.onItemIconClick(e) }></i>
                <span>{ item.classification_name }</span>
              </div>
              { item.children && item.children.length > 0 && this.generateTree(item.children) }
            </li>
          )
        }) }
      </ul>
    )
  }

  showList() {
    this.dplList.className += ' show'
  }

  hideList() {
    this.dplList.className = this.dplList.className.replace(/\s?show\s?/, '')
  }

  onItemClick(e) {
    e.stopPropagation()

    const target = e.currentTarget
    const key = target.getAttribute('data-key')
    const value = target.getAttribute('data-value')

    this.hideList()
    this.ipt.value = value
    this.ipt.setAttribute('data-key', key)
  }

  onItemIconClick(e) {
    e.stopPropagation()

    const parent = e.currentTarget.parentNode
    const list = parent.parentNode.querySelector('ul')
    const icon = parent.querySelector('i')

    if (list) {
      if (list.className) {
        list.removeAttribute('class')
      } else {
        list.className = 'show'
      }
    }

    if (icon.className.indexOf('file') === -1) {
      if (icon.className.match('category-open')) {
        icon.className = 'crm-icon crm-icon-category'
      } else {
        icon.className = 'crm-icon crm-icon-category-open'
      }
    }
  }

  onArrowClick(e) {
    e.preventDefault()

    if (this.dplList.className.match('show')) {
      this.hideList()
    } else {
      this.getData()
      this.showList()
    }
  }

  render() {
    return (
      <div className="crm-dpl group-tree-dpl" onClick={ e => e.stopPropagation() }>
        <div className="value">
          <input type="text" placeholder="--请选择--" ref={ ipt => this.ipt = ipt } readOnly { ...this.state.rest } />
          <a className="fa fa-angle-down" href="#" onClick={ e => this.onArrowClick(e) }></a>
        </div>
        <div className="list" ref={ dplList => this.dplList = dplList }>
          { this.state.data.length > 0 && this.generateTree(this.state.data) }
        </div>
      </div>
    )
  }
}

export default GroupTreeDropdown
