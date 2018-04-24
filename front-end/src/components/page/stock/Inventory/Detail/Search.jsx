import React from 'react'
import InputText from '../../../../widget/form/InputText'
import InputDateTimeGroup from '../../../../widget/form/InputDateTimeGroup'
import genBasicSearch from '../../../../HOC/genBasicSearch'

function BodyComponent() {
  return (
    <div>
      <InputText data-id="search-number" text="盘点单号" />
      <InputDateTimeGroup />
    </div>
  )
}

function onSearch({ pageInstance, apiInfo }) {
  apiInfo.data.data.number = pageInstance.page.querySelector('[data-id="search-number"]').value
  apiInfo.data.data.startTime = pageInstance.page.querySelectorAll('.crm-datetime-group input')[0].value
  apiInfo.data.data.endTime = pageInstance.page.querySelectorAll('.crm-datetime-group input')[1].value
  pageInstance.getData(1)
  apiInfo.data.data.number = ''
  apiInfo.data.data.startTime = apiInfo.data.data.endTime = ''
}

export default genBasicSearch({
  BodyComponent,
  onSearch
})
