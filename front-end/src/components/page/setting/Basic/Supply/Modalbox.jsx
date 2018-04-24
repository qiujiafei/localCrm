import React from 'react'
import genBasicModalbox from '../../../../HOC/genBasicModalbox'

function BodyComponent() {
  return (
    <table>
      <tbody>
        <tr>
          <td><span className="crm-color crm-color-error">*</span>供应商名称</td>
          <td>
            <div><input data-id="main_name" data-name="供应商名称" type="text" maxLength="30" placeholder="输入供应商名称" required /></div>
          </td>
          <td><span className="crm-color crm-color-error">*</span>联系人姓名</td>
          <td>
            <div><input data-id="contact_name" data-name="联系人姓名" type="text" maxLength="30" placeholder="输入联系人姓名" required /></div>
          </td>
        </tr>
        <tr>
          <td><span className="crm-color crm-color-error">*</span>手机号码</td>
          <td>
            <div><input data-id="cell_number" data-name="手机号码" type="text" maxLength="11" placeholder="输入手机号码" required /></div>
          </td>
          <td>联系电话</td>
          <td>
            <div>
              <input
                data-id="phone_number"
                data-name="联系电话"
                type="text"
                maxLength="30"
                placeholder="输入联系电话"
                onInput={
                  e => {
                    e.currentTarget.value = e.currentTarget.value.replace(/[^-\d]/, '')
                  }
                }
              />
            </div>
          </td>
        </tr>
        <tr>
          <td>联系地址</td>
          <td colSpan="3">
            <div><input data-id="address" data-name="联系地址" type="text" maxLength="100" placeholder="输入联系地址" /></div>
          </td>
        </tr>
        <tr>
          <td>结算方式</td>
          <td colSpan="3">
            <div><input data-id="pay_method" data-name="结算方式" type="text" maxLength="30" placeholder="输入结算方式" /></div>
          </td>
        </tr>
        <tr>
          <td>银行账号</td>
          <td>
            <div><input data-id="bank_account_ownner_name" data-name="开户人姓名" type="text" maxLength="30" placeholder="开户人姓名" /></div>
          </td>
          <td style={ { backgroundColor: '#fff' } }>
            <div><input data-id="bank_create_account_bank_name" data-name="开户行" type="text" maxLength="30" placeholder="开户行" /></div>
          </td>
          <td>
            <div><input data-id="bank_card_number" data-name="银行卡账号" type="text" maxLength="19"
              onInput={
                e => {
                  e.currentTarget.value = e.currentTarget.value.replace(/[^-\d]/, '')
                }
              }
              placeholder="银行卡账号" /></div>
          </td>
        </tr>
        <tr>
          <td>纳税人识别号</td>
          <td colSpan="3">
            <div><input data-id="taxpayer_identification_number" data-name="输入纳税人识别号" type="text" maxLength="60" placeholder="输入纳税人识别号" /></div>
          </td>
        </tr>
        <tr>
          <td>备注</td>
          <td colSpan="3">
            <div><textarea data-id="comment" data-name="输入备注" maxLength="200" placeholder="输入备注"></textarea></div>
          </td>
        </tr>
      </tbody>
    </table>
  )
}

export default genBasicModalbox({
  name: '供应商',
  width: '900',
  height: '500',
  BodyComponent: BodyComponent
})
