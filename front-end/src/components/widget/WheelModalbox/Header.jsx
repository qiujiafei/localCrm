import React, { Component } from 'react'
import PropTypes from 'prop-types'
import InputText from '../form/InputText'
import Button from '../Button'
import ajax from '../../../lib/ajax'


class Header extends Component {
  static propTypes = {
    apiInfo: PropTypes.object,
    pageInstance: PropTypes.object
  }

  constructor(props) {
    super(props)
  }

  onAdd() {
    const { apiInfo, pageInstance } = this.props
    const element = this.wrapper.querySelector('[data-id="brand_name"]')
    const brandName = element.value

    if (brandName && brandName !==" ") {
      pageInstance.showIndicator()

      ajax({
        method: 'POST',
        url: '/customercarstirebrand/put/insert.do',
        data: {
          brand_name: brandName
        }
      }).then(info => {
        pageInstance.hideIndicator()
        if (info.err) {
          pageInstance.showToolTip(info.desc, 'failed')

          if (info.goToLogin) {
            setTimeout(() => location.href = '/login', 3000)
          }
        } else {
          element.value = ''
          apiInfo.data.page_num = 1
          pageInstance.getData()
        }
      })
    } else {
      pageInstance.showToolTip('请输入正确名称', 'failed')
    }
  }

  render() {
    return (
      <div ref={ wrapper => this.wrapper = wrapper }>
        <InputText data-id="brand_name" text="品牌名称" />
        <Button text="添加" type="secondary" onClick={ e => this.onAdd(e) } />
      </div>
    )
  }
}

export default Header
