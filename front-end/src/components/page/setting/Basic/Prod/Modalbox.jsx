import React, { Component } from 'react'
import PropTypes from 'prop-types'
import genBasicModalbox from '../../../../HOC/genBasicModalbox'
import { CommonDropdown } from '../../../../widget/Dropdown/Dropdown'
import DepotPlainDropdown from '../../../../widget/DepotPlainDropdown'
import UnitPlainDropdown from '../../../../widget/UnitPlainDropdown'
import ProdGroupPlainDropdown from '../../../../widget/ProdGroupPlainDropdown'
import DepotModalbox from '../../../../widget/DepotModalbox/DepotModalbox'
import ProdGroupModalbox from '../../../../widget/ProdGroupModalbox/ProdGroupModalbox'
import UnitModalbox from '../../../../widget/UnitModalbox/UnitModalbox'
import util from '../../../../../lib/util'

class BodyComponent extends Component {
  static propTypes = {
    pageInstance: PropTypes.object
  }

  constructor(props) {
    super(props)

    this.state = {
      depotModalboxDisplay: false,
      unitModalboxDisplay: false,
      prodGroupModalboxDisplay: false
    }
  }

  displayModalbox(type, display) {
    this.setState({ [ type + 'ModalboxDisplay' ]: display })
  }


  render() {
    return (
      <div ref={ wrapper => this.wrapper = wrapper }>

        { this.state.depotModalboxDisplay && (
          <DepotModalbox
            parentPage={ this.props.pageInstance }
            transferTrData={ data => {
              const { original } = data.rowInfo
              const depotElement = this.wrapper.querySelector('[data-id="default_depot_id"]')
              const element = this.wrapper.querySelector('[data-id="depot_name"]')

              element.value = original.depot_name
              depotElement.value = original.id

              this.displayModalbox('depot', false)
            } }
            handleClose={ () => this.displayModalbox('depot', false) }
          />
        )}

        {
          this.state.unitModalboxDisplay && (
            <UnitModalbox
              parentPage={ this.props.pageInstance }
              transferTrData={ data => {
                const { original } = data.rowInfo
                const element = this.wrapper.querySelector('[data-id="unit_name"]')

                element.value = original.unit_name

                this.displayModalbox('unit', false)
              } }
              handleClose={ () => this.displayModalbox('unit', false) }
            />
          )
        }

        {
          this.state.prodGroupModalboxDisplay && (
            <ProdGroupModalbox
              type="选择"
              show={ true }
              pageInstance={ this.props.pageInstance }
              handleSubmitClick={
                (e, modalbox) => {
                  const thirdGroup = modalbox.querySelector('.cell:nth-of-type(3) li.on')

                  if (!thirdGroup) {
                    this.props.pageInstance.showTip('请选择三级分类', 'failed')
                    return
                  }

                  this.wrapper.querySelector('[data-id="classification_name"]').value = thirdGroup.title
                  this.displayModalbox('prodGroup', false)
                }
              }
              handleCancelClick={ () => this.displayModalbox('prodGroup', false) }
              handleCloseClick={ () => this.displayModalbox('prodGroup', false) }
            />
          )
        }

        <table>
          <tbody>
            <tr>
              <td>
                <label><span className="crm-color crm-color-error">*</span>条形码</label>
              </td>
              <td>
                <div>
                  <input
                    data-id="barcode"
                    data-name="条形码"
                    type="text"
                    maxLength="13"
                    placeholder="输入条形码"
                    onInput={ e => e.currentTarget.value = util.formateToInteger(e.currentTarget.value) }
                    required
                  />
                </div>
              </td>
              <td><label>商品编码</label></td>
              <td>
                <div>
                  <input
                    data-id="commodity_code"
                    data-name="商品编码"
                    type="text"
                    maxLength="20"
                    placeholder="输入商品编码"
                  />
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <label><span className="crm-color crm-color-error">*</span>商品名称</label>
              </td>
              <td>
                <div>
                  <input
                    data-id="commodity_name"
                    data-name="商品名称"
                    type="text"
                    maxLength="20"
                    placeholder="输入商品名称"
                    required
                  />
                </div>
              </td>
              <td><label>型号规格</label></td>
              <td>
                <div>
                  <input
                    data-id="specification"
                    data-name="型号规格"
                    type="text"
                    maxLength="20"
                    placeholder="输入型号规格"
                  />
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <label><span className="crm-color crm-color-error">*</span>售价</label>
              </td>
              <td>
                <div>
                  <input
                    data-id="price"
                    data-name="售价"
                    type="text"
                    maxLength="13"
                    placeholder="输入售价"
                    onInput={ e => e.currentTarget.value = util.formateToDecimal(e.currentTarget.value) }
                    onBlur={ e => {
                      if (e.currentTarget.value) {
                        e.currentTarget.value = parseFloat(e.currentTarget.value).toFixed(2)
                      } else {
                        e.currentTarget.value = ''
                      }
                    } }
                    required
                  />
                </div>
              </td>
              <td>
                <label><span className="crm-color crm-color-error">*</span>所属分类</label>
              </td>
              <td className="for-dropdown">
                <div>
                  <ProdGroupPlainDropdown data-id="classification_name" data-name="所属分类" handleArrowClick={ () => this.displayModalbox('prodGroup', true) } required />
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <label><span className="crm-color crm-color-error">*</span>计量单位</label>
              </td>
              <td className="for-dropdown">
                <div>
                  <UnitPlainDropdown data-id="unit_name" data-name="计量单位" handleArrowClick={ () => this.displayModalbox('unit', true) } required />
                </div>
              </td>
              <td><label>仓库</label></td>
              <td className="for-dropdown">
                <div>
                  <input type="hidden" data-id="default_depot_id" />
                  <DepotPlainDropdown data-id="depot_name" handleArrowClick={ () => this.displayModalbox('depot', true) } />
                </div>
              </td>
            </tr>
            <tr>
              <td><label>配件属性</label></td>
              <td className="for-dropdown">
                <div>
                  <CommonDropdown
                    data-id="commodity_property_name"
                    data-name="配件属性"
                    placeholder="请选择配件属性"
                    data={
                      [
                        { id: 1, name: '其他' },
                        { id: 2, name: '原厂' },
                        { id: 3, name: '同质' },
                        { id: 4, name: '修复' }
                      ]
                    }
                  />
                </div>
              </td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td><label>备注</label></td>
              <td colSpan="3">
                <div>
                  <textarea data-id="comment" data-name="备注" placeholder="输入备注" maxLength="200"></textarea>
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
  name: '商品',
  width: '960',
  height: '540',
  BodyComponent: BodyComponent
})
