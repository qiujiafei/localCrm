import React, { Component } from 'react'
import PropTypes from 'prop-types'
import InputText from '../../../widget/form/InputText'
import genBasicSearch from '../../../HOC/genBasicSearch'
import genInputDropdown from '../../../HOC/genInputDropdown'

const InputMember = genInputDropdown()

class BodyComponent extends Component {
  static propTypes = {
    pageInstance: PropTypes.object
  }

  constructor(props) {
    super(props)
  }

  render() {
    return (
      <div>
        <InputMember data-id="is_member" text="是否会员" data={ [ { id: 1, name: '是' }, { id: 0, name: '否' } ] } />
        <InputText data-id="search-kw" text="关键字" placeholder="姓名/手机/车牌/车架号" />
      </div>
    )
  }
}

function onSearch({ pageInstance, apiInfo }) {
  apiInfo.data.data.keyword = pageInstance.page.querySelector('[data-id="search-kw"]').value
  apiInfo.data.data.is_member = pageInstance.page.querySelector('[data-id="is_member"]').getAttribute('data-key')

  pageInstance.getData(1)

  apiInfo.data.data.keyword = ''
  apiInfo.data.data.is_member = ''
}

export default genBasicSearch({
  BodyComponent,
  onSearch
})
