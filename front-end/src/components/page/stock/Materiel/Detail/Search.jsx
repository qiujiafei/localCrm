import React from 'react'
import InputText from '../../../../widget/form/InputText'
import genBasicSearch from '../../../../HOC/genBasicSearch'

function BodyComponent() {
  return (
    <div>
      <InputText data-id="search-number" text="单号" />
      <InputText data-id="search-commodity" text="商品名称" />
    </div>
  )
}

function onSearch({ pageInstance, apiInfo }) {
  apiInfo.data.data.number = pageInstance.page.querySelector('[data-id="search-number"]').value
  apiInfo.data.data.commodity_name = pageInstance.page.querySelector('[data-id="search-commodity"]').value
  pageInstance.getData(1)
  apiInfo.data.data.number = ''
  apiInfo.data.data.commodity_name = ''
}

export default genBasicSearch({
  BodyComponent,
  onSearch
})
