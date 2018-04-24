import React, { Component } from 'react'
import PropTypes from 'prop-types'
import genBasicModalbox from '../../../../../HOC/genBasicModalbox'
import ProdGroupPlainDropdown from '../../../../../widget/ProdGroupPlainDropdown'
import ProdGroup2Modalbox from '../../../../../widget/ProdGroup2Modalbox/ProdGroup2Modalbox'

class BodyComponent extends Component {
  static propTypes = {
    pageInstance: PropTypes.object
  }

  constructor(props) {
    super(props)

    this.state = {
      prodGroup2ModalboxDisplay: false
    }
  }

  displayModalbox(type, display) {
    this.setState({ [ type + 'ModalboxDisplay' ]: display })
  }

  render() {
    return (
      <div ref={ wrapper => this.wrapper = wrapper }>

        <ProdGroup2Modalbox
          type="选择"
          show={ this.state.prodGroup2ModalboxDisplay }
          pageInstance={ this.props.pageInstance }
          handleSubmitClick={
            (e, modalbox) => {
              const selectedElement = modalbox.querySelector('li.on')

              this.wrapper.querySelector('[data-id="parent_name"]').value = selectedElement.title
              this.wrapper.querySelector('[data-id="parent_id"]').value = selectedElement.getAttribute('data-id')

              this.displayModalbox('prodGroup2', false)
            }
          }
          handleCancelClick={ () => this.displayModalbox('prodGroup2', false) }
          handleCloseClick={ () => this.displayModalbox('prodGroup2', false) }
        />

        <table>
          <tbody>
            <tr>
              <td>
                <span className="crm-color crm-color-error">*</span>分类名称
              </td>
              <td>
                <div>
                  <input data-id="classification_name" data-name="分类名称" type="text" placeholder="输入分类名称" maxLength="30" required />
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <span className="crm-color crm-color-error">*</span>所属分类
              </td>
              <td className="for-dropdown">
                <div>
                  <input data-id="parent_id" data-name="所属分类" type="hidden" required />
                  <ProdGroupPlainDropdown data-id="parent_name" handleArrowClick={ () => this.displayModalbox('prodGroup2', true) } />
                </div>
              </td>
            </tr>
            <tr>
              <td>备注</td>
              <td>
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
  name: '分类',
  width: '540',
  height: '360',
  BodyComponent: BodyComponent
})
