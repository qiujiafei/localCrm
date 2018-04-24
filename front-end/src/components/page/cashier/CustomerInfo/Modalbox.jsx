import React, { Component } from 'react'
import PropTypes from 'prop-types'
import genBasicModalbox from '../../../HOC/genBasicModalbox'
// import Checkbox from '../../../widget/Checkbox/Checkbox'
import util from "../../../../lib/util"
import Radio from '../../../widget/Radio'
import InputDateTime from '../../../widget/form/InputDateTime'
import InputFileButton from '../../../widget/InputFileButton'
import DropdownProvince from '../../../widget/DropdownProvince'
import DropdownAlphabet from '../../../widget/DropdownAlphabet'
import InputWheel from '../../../widget/InputWheel'
import InputTypeOfCar from '../../../widget/InputTypeOfCar'
import WheelModalbox from '../../../widget/WheelModalbox/WheelModalbox'
import EditModalbox from '../../../widget/WheelModalbox/EditModalbox'
import TypeOfCarModalbox from '../../../widget/TypeOfCarModalbox/TypeOfCarModalbox'
import ajax from '../../../../lib/ajax'

class BodyComponent extends Component {
  static propTypes = {
    pageInstance: PropTypes.object
  }

  constructor(props) {
    super(props)

    const { pageInstance } = this.props

    this.state = {
      wheelModalboxDisplay: false,
      typeOfCarModalboxDisplay: false,
      editModalboxDisplay: false,
      editModalboxIndicatorDisplay: false,
    }

    pageInstance.showEditModalbox = () => { this.setState({ editModalboxDisplay: true }) }
    pageInstance.hideEditModalbox = () => { this.setState({ editModalboxDisplay: false }) }
  }

  handleTypeOfCarArrowClick() {
    this.setState({ typeOfCarModalboxDisplay: true })
  }

  showIndicator() {
    this.setState({ editModalboxIndicatorDisplay: true })
  }

  hideIndicator() {
    this.setState({ editModalboxIndicatorDisplay: false })
  }

  transferTrData(data) {
    const { row } = data.rowInfo

    this.wrapper.querySelector('[data-id="tire_brand_id"]').value = row.id
    this.wrapper.querySelector('[data-id="tire_brand_name"]').value = row.brand_name

    this.setState({ wheelModalboxDisplay: false })
  }

  render() {
    return (
      <div ref={ wrapper => this.wrapper = wrapper }>
        { this.state.wheelModalboxDisplay && (
            <WheelModalbox
              ref={ wheelModalbox => this.wheelModalbox = wheelModalbox }
              parentPage={ this.props.pageInstance }
              transferTrData={ data => this.transferTrData(data) }
              handleClose={ () => this.setState({ wheelModalboxDisplay: false }) }
            />
          )
        }
        <EditModalbox
          type="编辑"
          displayIndicator={ this.state.editModalboxIndicatorDisplay }
          pageInstance={ this.props.pageInstance }
          show={ this.state.editModalboxDisplay }
          handleCloseClick={ () => this.props.pageInstance.hideEditModalbox() }
          handleSubmitClick={ (e, wrapper) => {

            this.showIndicator()

            ajax({
              method: 'POST',
              url: '/customercarstirebrand/modify/modify.do',
              data: {
                id: this.props.pageInstance.editId,
                brand_name: wrapper.querySelector('[data-id="brand_name"]').value
              }
            }).then((info) => {
              if (info.err) {
                this.props.pageInstance.showTip(info.desc, 'failed')

                if (info.goToLogin) {
                  setTimeout(() => location.href = '/login', 3000)
                }
              } else {
                this.props.pageInstance.hideEditModalbox()
                this.wheelModalbox.getData()
              }

              this.hideIndicator()
            })
          } }
          handleCancelClick={ () => {
            this.props.pageInstance.hideEditModalbox()
          } }
        />

        <h5 style={ { 'width': '90%', 'margin': '0 auto', 'heigth': '30px', 'lineHeight': '30px' } }>客户信息</h5>
        <table>
          <tbody>
            <tr>
              <td><span className="crm-color crm-color-error">*</span>客户姓名</td>
              <td>
                <div>
                  <input
                    data-id="customer_name"
                    data-name="客户姓名"
                    type="text"
                    maxLength="30"
                    placeholder="输入姓名"
                    required
                  />
                </div>
              </td>
              <td><span className="crm-color crm-color-error">*</span>手机号码</td>
              <td>
                <div>
                  <input
                    data-id="cellphone_number"
                    data-name="手机号码"
                    type="text"
                    maxLength="11"
                    placeholder="输入手机号"
                    required
                    onInput={ e => e.currentTarget.value = util.formateToInteger(e.currentTarget.value) }/>
                </div>
              </td>
              <td><span className="crm-color crm-color-error">*</span>性别</td>
              <td>
                <div>
                  <Radio name="sex" data-id='gender' data-value="1" checked />男
                  <Radio name="sex" data-id='gender' data-value="0" />女
                </div>
              </td>
            </tr>
            <tr>
              <td>生日</td>
              <td>
                <InputDateTime
                  noLabel
                  data-id="birthday"
                  icon="date"
                  placeholder="输入生日"
                  dateOption={ {
                    dateFmt: 'yyyy-MM-dd'
                  } }
                />
              </td>
              <td>身份证</td>
              <td>
                <div>
                  <input
                    type="text"
                    data-id="ID_number"
                    maxLength="18"
                    placeholder="输入身份证"
                    onInput={ e => {
                      e.currentTarget.value = e.currentTarget.value.replace(/[^0-9a-zA-Z]/g, '')
                    } }
                  />
                </div>
              </td>
              <td>地址</td>
              <td>
                <div>
                  <input
                    type="text"
                    data-id="address"
                    placeholder="输入地址"
                    maxLength="50"
                  />
                </div>
              </td>
            </tr>
            <tr>
              <td><span className="crm-color crm-color-error">*</span>客户来源</td>
              <td>
                <div>
                  <input
                    type="text"
                    data-id="customer_origination"
                    data-name="客户来源"
                    placeholder="输入客户来源"
                    maxLength="30"
                    required
                  />
                </div>
              </td>
              <td>上传驾驶证</td>
              <td>
                <div>
                  <InputFileButton data-id="license_image_name" name="驾驶证" pageInstance={ this.props.pageInstance } />
                </div>
              </td>
              <td>单位名称</td>
              <td>
                <div>
                  <input
                    data-id="company"
                    type="text"
                    placeholder="输入单位名称"
                    maxLength="50"
                  />
                </div>
              </td>
            </tr>
            <tr>
              <td>备注</td>
              <td colSpan="5">
                <div>
                  <input
                    data-id="comment"
                    data-name="备注"
                    type="text"
                    maxLength="200"
                    placeholder="输入备注"
                  />
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <h5 style={ { 'width': '90%', 'margin': '0 auto', 'heigth': '30px', 'lineHeight': '30px' } }>车辆信息</h5>
        <table>
          <tbody>
            <tr>
              <td><span className="crm-color crm-color-error">*</span>车架号</td>
              <td>
                <div>
                  <input
                    data-id="frame_number"
                    data-name="车架号"
                    type="text"
                    placeholder="输入车架号"
                    maxLength="30"
                    required
                  />
                </div>
              </td>
              <td><span className="crm-color crm-color-error">*</span>车牌</td>
              <td className="for-dropdown">
                <div>
                  <DropdownProvince
                    data-id="number_plate_province_id"
                    wrapperStyle={ {
                      display: 'inline-block',
                      width: '80px',
                      verticalAlign: 'middle',
                      fontSize: '13px'
                    } }
                  />
                  <DropdownAlphabet
                    data-id="number_plate_alphabet_id"
                    wrapperStyle={ {
                      display: 'inline-block',
                      width: '80px',
                      verticalAlign: 'middle',
                      fontSize: '13px'
                    } }
                  />
                  <input
                    style={ {
                      display: 'inline-block',
                      width: '100px',
                      verticalAlign: 'middle'
                    } }
                    type="text"
                    data-id="number_plate_number"
                    data-name="车牌"
                    maxLength="6"
                    placeholder="输入车牌号"
                    required
                  />
                </div>
              </td>
              <td><span className="crm-color crm-color-error">*</span>品牌车型</td>
                <td>
                  <InputTypeOfCar data-id="model_id" handleArrowClick={ () => this.handleTypeOfCarArrowClick() } placeholder="选择品牌车型" noLabel />
                  <TypeOfCarModalbox
                    pageInstance={ this.props.pageInstance }
                    show={ this.state.typeOfCarModalboxDisplay }
                    handleSubmitClick={
                      () => {
                        const element = document.querySelector('.item.modal li.on')
                        const targetElement = this.wrapper.querySelector('[data-id="model_id"]')

                        targetElement.value = element.getAttribute('data-style-name')
                        targetElement.setAttribute('data-key', element.getAttribute('data-id'))
                        this.setState({ typeOfCarModalboxDisplay: false })
                      }
                    }
                    handleCloseClick={ () => this.setState({ typeOfCarModalboxDisplay: false }) }
                    handleCancelClick={ () => this.setState({ typeOfCarModalboxDisplay: false }) }
                  />
                </td>
            </tr>
            <tr>
              <td>排量</td>
              <td>
                <div>
                  <input
                    data-id="vehicle_displacement"
                    type="text"
                    placeholder="输入排量"
                    maxLength="30"
                  />
                </div>
              </td>
              <td>车价（万）</td>
              <td>
                <div>
                  <input
                    type="text"
                    data-id="vehicle_price"
                    placeholder="输入车价"
                    maxLength="10"
                    onInput={ e => e.currentTarget.value = util.formateToDecimal(e.currentTarget.value) }
                  />
                </div>
              </td>
              <td>发动机型号</td>
              <td>
                <div>
                  <input
                    data-id="engine_model"
                    type="text"
                    maxLength="30"
                    placeholder="输入发动机型号"
                  />
                </div>
              </td>
            </tr>
            <tr>
              <td>厂牌型号</td>
              <td>
                <div>
                  <input
                    data-id="manufacturer"
                    type="text"
                    placeholder="输入厂牌型号"
                    maxLength="30"
                  />
                </div>
              </td>
              <td>发动机号码</td>
              <td>
                <div>
                  <input
                    data-id="engine_number"
                    type="text"
                    placeholder="输入发动机号码"
                    maxLength="30"
                  />
                </div>
              </td>
              <td>漏油检查</td>
              <td>
                <div>
                  <input
                    data-name="漏油检查"
                    data-id="leakage_status"
                    type="text"
                    placeholder="输入漏油检查"
                    maxLength="30"
                  />
                </div>
              </td>
            </tr>
            <tr>
              <td>上传行驶证</td>
              <td>
                <InputFileButton data-id="vehicle_license_image_name" name="行驶证" pageInstance={ this.props.pageInstance } />
              </td>
              <td>下次保养里程（km）</td>
              <td>
                <div>
                  <input
                    data-id="next_service_mileage"
                    type="text"
                    placeholder="输入下次保养里程"
                    maxLength="10"
                    onInput={ e => e.currentTarget.value = util.formateToDecimal(e.currentTarget.value) }
                  />
                </div>
              </td>
              <td>上次里程（km）</td>
              <td>
                <div>
                  <input
                    data-id="prev_service_mileage"
                    type="text"
                    maxLength="10"
                    placeholder="上次里程"
                    onInput={ e => e.currentTarget.value = util.formateToDecimal(e.currentTarget.value) }
                  />
                </div>
              </td>
            </tr>
            <tr>
              <td>轮胎检查</td>
              <td>
                <div>
                  <input
                    data-id="tire_status"
                    placeholder="轮胎检查"
                    maxLength="30"
                    type="text"
                  />
                </div>
              </td>
              <td>车辆颜色</td>
              <td>
                <div>
                  <input
                    data-id="color"
                    type="text"
                    placeholder="车辆颜色"
                    maxLength="10"
                  />
                </div>
              </td>
              <td>刹车片检查</td>
              <td>
                <div>
                  <input
                    data-id="break_status"
                    type="text"
                    placeholder="输入刹车片检查"
                    maxLength="30"
                  />
                </div>
              </td>
            </tr>
            <tr>
              <td>刹车油检查</td>
              <td>
                <div>
                  <input
                    data-id="break_oil_status"
                    placeholder="输入刹车油检查"
                    type="text"
                    maxLength="30"
                  />
                </div>
              </td>
              <td>电瓶检查</td>
              <td>
                <div>
                  <input
                    data-id="bettry_status"
                    placeholder="输入电瓶检查"
                    type="text"
                    maxLength="30"
                  />
                </div>
              </td>
              <td>机油检查</td>
              <td>
                <div>
                  <input
                    type="text"
                    placeholder="输入机油检查"
                    data-id="lubricating_oil_status"
                    maxLength="30"
                  />
                </div>
              </td>
            </tr>
            <tr>
              <td>保险公司</td>
              <td>
                <div>
                  <input
                    type="text"
                    placeholder="输入保险公司"
                    data-id="insurance_company"
                    maxLength="30"
                  />
                </div>
              </td>
              <td>故障灯检查</td>
              <td>
                <div>
                  <input
                    type="text"
                    placeholder="输入故障灯检查"
                    data-id="fault_light"
                    maxLength="30"
                  />
                </div>
              </td>
              <td>轮胎品牌</td>
              <td>
                <input data-id="tire_brand_id" type="hidden" />
                <InputWheel
                  data-id="tire_brand_name"
                  maxLength="30"
                  handleArrowClick={
                    () => { this.setState({ wheelModalboxDisplay: true }) }
                  }
                  noLabel
                />
              </td>
            </tr>
            <tr>
              <td>轮胎型号</td>
              <td>
                <div>
                  <input
                    type="text"
                    placeholder="轮胎型号"
                    data-id="tire_specification"
                    maxLength="30"
                  />
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    )
  }
}

export default genBasicModalbox({
  name: '客户资料',
  width: '1320',
  height: '680',
  BodyComponent: BodyComponent
})
