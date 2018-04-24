import React from 'react'
import genBasicSearch from '../../../HOC/genBasicSearch'
import genRadioButtonGroup from '../../../HOC/genRadioButtonGroup'
import InputDateTimeGroup from '../../../widget/form/InputDateTimeGroup'
import SupplyDropdown from '../../../widget/form/dropdown/Supply'

const RadioButtonGroup = genRadioButtonGroup({
  name: 'type',
  buttons: [
    {
      id: 'today',
      text: '本日'
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
      <div className="field-wrapper">
        <label>供应商</label>
        <div>
          <SupplyDropdown />
        </div>
      </div>
      <RadioButtonGroup />
      <InputDateTimeGroup />
    </div>
  )
}

function onSearch({ pageInstance }) {
  const searchTime = pageInstance.wrapper.querySelector('.crm-radio-button-group .on ').getAttribute('data-id').split('-')[1]
  const startDate = pageInstance.wrapper.querySelectorAll('.crm-datetime-group input')[0].value
  const endDate = pageInstance.wrapper.querySelectorAll('.crm-datetime-group input')[1].value
  let supplier_id = pageInstance.wrapper.querySelectorAll('.value input')[0].getAttribute('data-key')
  pageInstance.getData({
    searchTime,
    startDate,
    endDate,
    supplier_id
  })
}

export default genBasicSearch({
  BodyComponent,
  onSearch
})
