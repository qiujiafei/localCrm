import React, { Component } from 'react'
import PropTypes from 'prop-types'
import genBasicModalbox from '../../../HOC/genBasicModalbox'
import Checkbox from '../../../widget/Checkbox/Checkbox'
import util from '../../../../lib/util'
import ServiceGroupPlainDropdown from '../../../widget/ServiceGroupPlainDropdown'
import ServiceGroupModalbox from '../../../widget/ServiceGroupModalbox/ServiceGroupModalbox'

class BodyComponent extends Component {
  static propTypes = {
    pageInstance: PropTypes.object
  }

  constructor(props) {
    super(props)

    this.state = {
      serviceGroupModalboxDisplay: false
    }
  }

  displayModalbox(type, display) {
    this.setState({ [ type + 'ModalboxDisplay' ]: display })
  }

  render() {
    return (
      <div ref={ wrapper => this.wrapper = wrapper }>
        {
          this.state.serviceGroupModalboxDisplay && (
            <ServiceGroupModalbox
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

                  this.wrapper.querySelector('[data-id="service_claasification_id"]').value = thirdGroup.getAttribute('data-id')
                  this.wrapper.querySelector('[data-id="service_claasification_name"]').value = thirdGroup.title

                  this.displayModalbox('serviceGroup', false)
                }
              }
              handleCancelClick={ () => this.displayModalbox('serviceGroup', false) }
              handleCloseClick={ () => this.displayModalbox('serviceGroup', false) }
            />
          )
        }

        <table>
          <tbody>
            <tr>
              <td><span className="crm-color crm-color-error">*</span>名称</td>
              <td>
                <div><input data-id="service_name" data-name="名称" type="text" maxLength="30" placeholder="输入名称" required /></div>
              </td>
              <td>售价</td>
              <td>
                <div><input data-id="price" data-name="售价" data-default="0.00" type="text" maxLength="13" placeholder="输入售价" defaultValue="0.00" onInput={ e => e.currentTarget.value = util.formateToDecimal(e.currentTarget.value) } /></div>
              </td>
            </tr>
            <tr>
              <td>规格</td>
              <td>
                <div><input data-id="specification" data-name="规格" type="text" maxLength="30" placeholder="输入规格" /></div>
              </td>
              <td><span className="crm-color crm-color-error">*</span>分类</td>
              <td className="for-dropdown">
                <div>
                  <input data-id="service_claasification_id" data-name="分类" type="hidden" required />
                  <ServiceGroupPlainDropdown data-id="service_claasification_name" handleArrowClick={ () => this.displayModalbox('serviceGroup', true) } />
                </div>
              </td>
            </tr>
            <tr>
              <td>自助项目</td>
              <td>
                <div>
                  <Checkbox
                    data-id="type"
                    data-name="自助项目"
                    onChange={ e => e.currentTarget.value = e.currentTarget.checked ? 1 : 0 }
                  />
                </div>
              </td>
              <td>备注</td>
              <td>
                <div><input data-id="comment" data-name="备注" maxLength="100" placeholder="输入备注" /></div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    )
  }
}

export default genBasicModalbox({
  name: '服务项目',
  width: '960',
  height: '400',
  BodyComponent: BodyComponent
})
