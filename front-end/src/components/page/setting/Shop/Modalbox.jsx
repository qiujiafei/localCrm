import React from 'react'
import genBasicModalbox from '../../../HOC/genBasicModalbox'
// import Radio from '../../../widget/Radio'

function BodyComponent() {
  return (
    <table>
      <tbody>
        <tr>
          <td>
            <label><span className="crm-color crm-color-error">*</span>店铺名称</label>
          </td>
          <td>
            <div>
              <input
                type="text"
                data-id="name"
                data-name="门店名称"
                placeholder="输入门店名称"
                readOnly
              />
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <label><span className="crm-color crm-color-error">*</span>电话</label>
          </td>
          <td>
            <div>
              <input
                type="text"
                maxLength="11"
                data-id="phone_number"
                data-name="电话"
                placeholder="输入手机号码"
                onInput = { e => {
                    const input = e.currentTarget
                    input.value = input.value.replace(/([^\d])/g,'')
                  }
                }
                readOnly
              />
            </div>
          </td>
        </tr>
        {/* <tr>
          <td>
            <label>设置为总店</label>
          </td>
          <td>
            <div>
              <label style={ { display: 'inline-block', marginRight: '5px' } }>
                <Radio data-id="is_main_store" />
              </label>
              <div style={ { display: 'inline-block' } }>是<span className="crm-color crm-color-error">*总店一旦设置将无法再进行修改，请慎重选择</span></div>
            </div>
          </td>
        </tr> */}
        <tr>
          <td>
            <label>定位坐标</label>
          </td>
          <td>
            <div>
              <input
                data-id="address"
                type="text"
                placeholder="输入门店地址"
              />
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  )
}

export default genBasicModalbox({
  name:'门店管理',
  width:'960',
  height:'300',
  BodyComponent: BodyComponent,
  displayFooter: false
})
