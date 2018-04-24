import React from 'react'
import genBasicModalbox from '../../../../HOC/genBasicModalbox'
import util from '../../../../../lib/util'

function BodyComponent() {
  return (
    <table>
      <tbody>
        <tr>
          <td>
            <span className="crm-color crm-color-error">*</span>项目名称
          </td>
          <td>
            <div>
              <input data-id="addition_name" data-name="项目名称" type="text" placeholder="输入项目名称" maxLength="30" required />
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <span className="crm-color crm-color-error">*</span>项目金额
          </td>
          <td>
            <div>
              <input
                data-id="price"
                data-name="项目金额"
                placeholder="输入项目金额"
                maxLength="30"
                required
                onInput={ e => e.currentTarget.value = util.formateToDecimal(e.currentTarget.value) }
                onBlur={ e => e.currentTarget.value = parseFloat(e.currentTarget.value).toFixed(2) }
              />
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  )
}

export default genBasicModalbox({
  name: '附加项目',
  width: '540',
  height: '280',
  BodyComponent: BodyComponent
})
