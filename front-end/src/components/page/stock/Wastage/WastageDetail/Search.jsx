import React from 'react'
import InputText from '../../../../widget/form/InputText'
import genBasicSearch from '../../../../HOC/genBasicSearch'

function BodyComponent() {
  return (
    <div>
      <InputText data-id="search-number" text="单号" />
    </div>
  )
}

function onSearch({ pageInstance, apiInfo }) {
  apiInfo.data.data.number = pageInstance.page.querySelector('[data-id="search-number"]').value
  pageInstance.getData(1)
  apiInfo.data.data.number = ''
}

export default genBasicSearch({
  BodyComponent,
  onSearch
})
