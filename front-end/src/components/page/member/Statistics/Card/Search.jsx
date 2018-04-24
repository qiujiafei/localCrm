import React from 'react'
import genBasicSearch from '../../../../HOC/genBasicSearch'
import genRadioButtonGroup from '../../../../HOC/genRadioButtonGroup'
import InputDateTimeGroup from '../../../../widget/form/InputDateTimeGroup'

const RadioButtonGroup = genRadioButtonGroup({
  name: 'type',
  buttons: [
    {
      id: '',
      text: '全部',
      selected: true
    },
    {
      id: '1',
      text: '本日'
    },
    {
      id: '2',
      text: '本月'
    },
    {
      id: '3',
      text: '本年'
    }
  ]
})

function BodyComponent() {
  return (
    <div>
      <RadioButtonGroup />
      <InputDateTimeGroup />
    </div>
  )
}

function onSearch({ pageInstance, apiInfo }) {
  apiInfo.data.type = pageInstance.wrapper.querySelector('.crm-radio-button-group label.on').getAttribute('data-id').split('-')[1]
  apiInfo.data.start_time = pageInstance.wrapper.querySelectorAll('.crm-datetime-group input')[0].value
  apiInfo.data.end_time = pageInstance.wrapper.querySelectorAll('.crm-datetime-group input')[1].value
  pageInstance.init()
  apiInfo.data.type = ''
  apiInfo.data.start_time = ''
  apiInfo.data.end_time = ''
}

export default genBasicSearch({
  BodyComponent,
  onSearch
})
