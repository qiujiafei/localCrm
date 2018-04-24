import React from 'react'
import InputDateTimeGroup from '../../../../widget/form/InputDateTimeGroup'
import genBasicSearch from '../../../../HOC/genBasicSearch'

function BodyComponent() {
  return (
    <div>
      <InputDateTimeGroup />
    </div>
  )
}

function onSearch({ pageInstance, apiInfo }) {
  const dateElements = pageInstance.page.querySelectorAll('.crm-datetime-group input')

  apiInfo.data.data.start_time = dateElements[0].value
  apiInfo.data.data.end_time = dateElements[1].value

  pageInstance.getData(1)

  apiInfo.data.data.start_time = ''
  apiInfo.data.data.end_time = ''
}

export default genBasicSearch({
  BodyComponent,
  onSearch
})
