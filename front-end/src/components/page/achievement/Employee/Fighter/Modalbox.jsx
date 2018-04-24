import React, { Component } from 'react'
import PropTypes from 'prop-types'
import genBasicModalbox from '../../../../HOC/genBasicModalbox'
import EmployeeType from '../../../../widget/form/dropdown/EmployeeType'
import InputFileButton from '../../../../widget/InputFileButton'

class BodyComponent extends Component {
  static propTypes = {
    pageInstance: PropTypes.object
  }

  constructor(props) {
    super(props)
  }

  render() {
    return (
      <table>
        <tbody>
          <tr>
            <td><span className="crm-color crm-color-error">*</span>姓名</td>
            <td>
              <div><input data-id="name" data-name="员工姓名" type="text" maxLength="10" placeholder="输入姓名" required /></div>
            </td>
            <td><span className="crm-color crm-color-error">*</span>工种</td>
            <td className="for-dropdown">
              <div>
                <EmployeeType data-id="employee_type_name" data-name="工种" type="text" placeholder="输入工种" required />
              </div>
            </td>
          </tr>
          <tr>
            <td>身份证照片</td>
            <td>
              <div>
                <InputFileButton data-id="ID_card_image" name="身份证" pageInstance={ this.props.pageInstance } />
              </div>
            </td>
            <td><span className="crm-color crm-color-error">*</span>手机</td>
            <td>
              <div>
                <input data-id="phone_number" data-name="手机" type="text" maxLength="11" placeholder="输入手机号码" required onInput={ e => e.currentTarget.value = e.currentTarget.value.replace(/[^\d]/g, '') } />
              </div>
            </td>
          </tr>
          <tr>
            <td>身份证</td>
            <td>
              <div>
                <input
                  data-id="ID_code"
                  data-name="身份证"
                  type="text"
                  maxLength="18"
                  placeholder="输入身份证"
                  onInput={ e => e.currentTarget.value = e.currentTarget.value.replace(/[^\w]/g, '') }
                />
              </div>
            </td>
            <td>QQ</td>
            <td>
              <div><input data-id="qq_number" data-name="QQ号" type="text" maxLength="30" placeholder="输入QQ号码" onInput={ e => e.currentTarget.value = e.currentTarget.value.replace(/[^\d]/g, '') } /></div>
            </td>
          </tr>
          <tr>
            <td>打卡密码</td>
            <td>
              <div><input data-id="attendance_code" data-name="打卡密码" type="text" maxLength="30" placeholder="输入打卡密码" /></div>
            </td>
            <td>底薪</td>
            <td>
              <div><input data-id="basic_salary" data-name="底薪" type="text" maxLength="30" placeholder="输入底薪" data-default="0.00" /></div>
            </td>
          </tr>
          <tr>
            <td>技师能力（%）</td>
            <td>
              <div><input data-id="ability" data-name="技师技能" type="text" maxLength="3" placeholder="输入技师技能" onInput={ e => e.currentTarget.value = e.currentTarget.value.replace(/[^\d]/g, '') } data-default="0" /></div>
            </td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td>备注</td>
            <td colSpan="3">
              <div><textarea data-id="comment" data-name="备注" type="text" maxLength="200" placeholder="输入备注"></textarea></div>
            </td>
          </tr>
        </tbody>
      </table>
    )
  }
}

export default genBasicModalbox({
  name: '员工资料',
  width: '960',
  height: '500',
  BodyComponent: BodyComponent
})
