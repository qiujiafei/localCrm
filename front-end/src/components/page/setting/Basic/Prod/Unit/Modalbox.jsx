import React from 'react'
import genBasicModalbox from '../../../../../HOC/genBasicModalbox'

function BodyComponent() {
  return (
    <table>
      <tbody>
        <tr>
          <td><span className="crm-color crm-color-error">*</span>单位名称</td>
          <td>
            <div>
              <input data-id="unit_name" data-name="单位名称" type="text" maxLength="10" placeholder="输入单位名称" required />
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  )
}

export default genBasicModalbox({
  name: '单位',
  width: '540',
  height: '236',
  BodyComponent: BodyComponent
})
