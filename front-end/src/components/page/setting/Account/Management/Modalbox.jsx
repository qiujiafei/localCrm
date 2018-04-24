import React from 'react'
import genBasicModalbox from '../../../../HOC/genBasicModalbox'
import ProfessionDpl from '../../../../widget/Dropdown/ProfessionDpl'

function BodyComponent() {
  return (
    <table>
      <tbody>
        <tr>
          <td>账号名称</td>
          <td>
            <div><input type="text" data-name="账号名称" data-id="account_name" placeholder='输入账号名称' maxLength="30" /></div>
          </td>
        </tr>
        <tr>
          <td><span className="crm-color crm-color-error">*</span>密码</td>
          <td>
            <div><input type="password" data-name="密码" data-id="passwd" maxLength="20" placeholder="输入密码" required /></div>
          </td>
        </tr>
        <tr>
          <td><span className="crm-color crm-color-error">*</span>确认密码</td>
          <td>
            <div><input type="password" data-name="确认密码" data-id="verify_passwd" maxLength="20" placeholder="再次输入密码" required /></div>
          </td>
        </tr>
        <tr>
          <td><span className="crm-color crm-color-error">*</span>绑定员工</td>
          <td className="for-dropdown">
            <div>
              <ProfessionDpl data-id="employee_id" data-name="绑定员工" type='text' placeholder='选择员工' required />
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  )
}

export default genBasicModalbox({
  name: '账号',
  width: '606',
  height: '340',
  BodyComponent: BodyComponent
})
