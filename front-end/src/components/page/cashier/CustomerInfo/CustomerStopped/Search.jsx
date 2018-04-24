import React from 'react'
import genBasicSearch from '../../../../HOC/genBasicSearch'

function BodyComponent() {
  return (
    <div>
      <div className="field-wrapper">
        <label>关键字</label>
        <div className="input-field">
          <input data-id="search-kw" type="text" maxLength="200" placeholder="输入关键字" />
        </div>
      </div>
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
