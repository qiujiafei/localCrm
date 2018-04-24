import React from 'react'
import InputSupply from '../../../../widget/form/InputSupply'
import InputText from '../../../../widget/form/InputText'
import InputDateTimeGroup from '../../../../widget/form/InputDateTimeGroup'
import genBasicSearch from '../../../../HOC/genBasicSearch'

function BodyComponent() {
  return (
    <div>
      <InputDateTimeGroup />
      <InputSupply data-id="supply-id" />
      <InputText data-id="search-kw" text="关键字" />
    </div>
  )
}

function onSearch({ pageInstance, apiInfo }) {
  apiInfo.data.data.keyWord = pageInstance.page.querySelector('[data-id="search-kw"]').value
  apiInfo.data.data.startTime = pageInstance.page.querySelectorAll('.crm-datetime-group input')[0].value
  apiInfo.data.data.endTime = pageInstance.page.querySelectorAll('.crm-datetime-group input')[1].value
  apiInfo.data.data.supplier_id = pageInstance.page.querySelector('[data-id="supply-id"]').getAttribute('data-key')  
  pageInstance.getData(1)
  apiInfo.data.data.keyWord = ''
  apiInfo.data.data.startTime = apiInfo.data.data.endTime = ''
  apiInfo.data.data.supplier_id = ''
}

export default genBasicSearch({
  BodyComponent,
  onSearch
})
