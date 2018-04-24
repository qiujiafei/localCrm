import React from 'react'
import genBasicSearch from '../../../../HOC/genBasicSearch'
import InputText from '../../../../widget/InputText'

function BodyComponent() {
  return (
    <div>
      <InputText
        data-id="search-kw"
        text="关键字"
      />
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
