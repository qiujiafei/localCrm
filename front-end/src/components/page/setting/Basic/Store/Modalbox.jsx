import React from 'react'
import genBasicModalbox from '../../../../HOC/genBasicModalbox'

function BodyComponent() {
  return (
    <table>
      <tbody>
        <tr>
          <td><span className="crm-color crm-color-error">*</span>仓库名称</td>
          <td>
            <div><input data-id="depot_name" data-name="仓库名称" type="text" placeholder="输入仓库名称" maxLength="30" required /></div>
          </td>
        </tr>
        <tr>
          <td>备注</td>
          <td>
            <div><textarea data-id="comment" data-name="备注" placeholder="输入备注" maxLength="200"></textarea></div>
          </td>
        </tr>
      </tbody>
    </table>
  )
}

export default genBasicModalbox({
  name: '仓库',
  width: '540',
  height: '350',
  BodyComponent: BodyComponent
})
