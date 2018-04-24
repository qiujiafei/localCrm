import React, { Component } from 'react'
import PropTypes from 'prop-types'
import genBasicSearch from '../../../HOC/genBasicSearch'
import InputText from '../../../widget/InputText'
import Input from '../../../widget/BasicInput'
import ServiceGroupModalbox from '../../../widget/ServiceGroupModalbox/ServiceGroupModalbox'
import ServiceGroupPlainDropdown from '../../../widget/ServiceGroupPlainDropdown'

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
    this.setState({ [ type + 'GroupModalboxDisplay' ]: display })
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

                  this.wrapper.querySelector('[data-id="search-group"]').value = thirdGroup.title
                  this.wrapper.querySelector('[data-id="search-group"]').setAttribute('data-key', thirdGroup.getAttribute('data-id'))

                  this.displayModalbox('service', false)
                }
              }
              handleCancelClick={ () => this.displayModalbox('service', false) }
              handleCloseClick={ () => this.displayModalbox('service', false) }
            />
          )
        }

        <InputText
          data-id="search-kw"
          text="关键字"
        />

        <Input
          data-id="search-group"
          text="服务分类"
          DplComponent={ ServiceGroupPlainDropdown }
          handleArrowClick={ () => this.displayModalbox('service', true) }
        />
      </div>
    )
  }
}

function onSearch({ pageInstance, apiInfo }) {
  apiInfo.data.data.keyword = pageInstance.page.querySelector('[data-id="search-kw"]').value
  apiInfo.data.data.service_claasification_id = pageInstance.page.querySelector('[data-id="search-group"]').getAttribute('data-key')

  pageInstance.getData(1)

  apiInfo.data.data.keyword = ''
  apiInfo.data.data.service_claasification_id = ''
}

export default genBasicSearch({
  BodyComponent,
  onSearch
})
