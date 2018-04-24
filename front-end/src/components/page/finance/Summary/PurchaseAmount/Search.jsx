import React from 'react'
import genBasicSearch from '../../../../HOC/genBasicSearch'
import genRadioButtonGroup from '../../../../HOC/genRadioButtonGroup'
import InputDateTimeGroup from '../../../../widget/form/InputDateTimeGroup'

const RadioButtonGroup = genRadioButtonGroup({
  name: 'today',
  buttons: [
    {
      id: 'today',
      text: '今日'
    },
    {
      id: 'month',
      text: '本月',
      selected: true
    },
    {
      id: 'year',
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

function onSearch({ pageInstance }) {
  const searchTime = pageInstance.wrapper.querySelector('.crm-radio-button-group .on ').getAttribute('data-id').split('-')[1]
  const startDate = pageInstance.wrapper.querySelectorAll('.crm-datetime-group input')[0].value
  const endDate = pageInstance.wrapper.querySelectorAll('.crm-datetime-group input')[1].value
  pageInstance.getData({
    searchTime,
    startDate,
    endDate,
    page: 1
  })

}

export default genBasicSearch({
  BodyComponent,
  onSearch
})
