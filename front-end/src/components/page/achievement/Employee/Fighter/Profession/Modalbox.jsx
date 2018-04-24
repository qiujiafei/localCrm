import React from 'react'
import genBasicModalbox from '../../../../../HOC/genBasicModalbox'

function BodyComponent() {
  return (
    <table>
      <tbody>
        <tr>
          <td><span className="crm-color crm-color-error">*</span>工种名称</td>
          <td>
            <div><input data-id="name" data-name="工种名称" type="text" maxLength="30" placeholder="输入工种" required /></div>
          </td>
        </tr>
        <tr>
          <td>备注</td>
          <td>
            <div><textarea data-id="comment" data-name="备注" maxLength="200" placeholder="输入备注"></textarea></div>
          </td>
        </tr>
      </tbody>
    </table>
  )
}

export default genBasicModalbox({
  name: '工种',
  width: '500',
  height: '300',
  BodyComponent: BodyComponent
})
