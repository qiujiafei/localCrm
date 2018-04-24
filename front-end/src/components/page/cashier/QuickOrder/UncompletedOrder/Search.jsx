import React from 'react'
import InputText from '../../../../widget/form/InputText'
import InputDateTimeGroup from '../../../../widget/form/InputDateTimeGroup'
import genBasicSearch from '../../../../HOC/genBasicSearch'

function BodyComponent() {
  return (
    <div>
      <InputDateTimeGroup />
      <InputText data-id="search-kw" text="关键字" />
    </div>
  )
}

function onSearch({ pageInstance, apiInfo }) {
  apiInfo.data.data.keyword = pageInstance.page.querySelector('[data-id="search-kw"]').value
  apiInfo.data.data.start_time = pageInstance.page.querySelectorAll('.crm-datetime-group input')[0].value
  apiInfo.data.data.end_time = pageInstance.page.querySelectorAll('.crm-datetime-group input')[1].value
  pageInstance.getData(1)
  apiInfo.data.data.keyword = ''
  apiInfo.data.data.start_time = apiInfo.data.data.end_time = ''
}

export default genBasicSearch({
  BodyComponent,
  onSearch
})
