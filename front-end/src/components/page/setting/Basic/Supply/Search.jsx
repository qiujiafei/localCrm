import React from 'react'
import genBasicSearch from '../../../../HOC/genBasicSearch'
import { CommonDropdown } from '../../../../widget/Dropdown/Dropdown'

function BodyComponent() {
  const data = [
    {
      id: 'main_name',
      name: '供应商名称'
    },
    {
      id: 'contact_name',
      name: '联系人'
    },
    {
      id: 'cell_number',
      name: '手机号码'
    },
    {
      id: 'phone_number',
      name: '联系电话'
    },
    {
      id: 'comment',
      name: '备注'
    }
  ]

  return (
    <div>
      <div className="field-wrapper">
        <div style={ { width: '100px', backgroundColor: '#fafafa' } }>
          <CommonDropdown data-id="search-category" data={ data } data-key={ data[0].id } defaultValue={ data[0].name } />
        </div>
        <div style={ { padding: '0 5px' } }>
          <input data-id="search-kw" type="text" maxLength="200" placeholder="输入关键字" />
        </div>
      </div>
    </div>
  )
}

function onSearch({ pageInstance, apiInfo }) {
  apiInfo.data.data.searchCategory = pageInstance.page.querySelector('[data-id="search-category"]').getAttribute('data-key')
  apiInfo.data.data.searchKeys = pageInstance.page.querySelector('[data-id="search-kw"]').value

  pageInstance.getData(1)

  // 查询后清空搜索值
  apiInfo.data.data.searchCategory = ''
  apiInfo.data.data.searchKeys = ''
}

export default genBasicSearch({
  BodyComponent,
  onSearch
})
