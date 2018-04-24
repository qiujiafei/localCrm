import React from 'react'
// import InputSupply from '../../widget/form/InputSupply'
import InputText from '../../../../widget/form/InputText'
import InputDateTimeGroup from '../../../../widget/form/InputDateTimeGroup'
import genBasicSearch from '../../../../HOC/genBasicSearch'

function BodyComponent() {
  return (
    <div>
      <InputText data-id="search-kw" text="关键字" />
      <InputDateTimeGroup />
    </div>
  )
}

function onSearch({ pageInstance, apiInfo }) {
  apiInfo.data.data.keyword = pageInstance.page.querySelector('[data-id="search-kw"]').value
  pageInstance.getData(1)
  apiInfo.data.data.keyword = ''
}

export default genBasicSearch({
  BodyComponent,
  onSearch
})
