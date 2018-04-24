import React, { Component } from 'react'
import PropTypes from 'prop-types'
import genBasicSearch from '../../../../HOC/genBasicSearch'
import Input from '../../../../widget/BasicInput'
// import BasicDropdown from '../../../../widget/BasicDropdown'
import ProdGroupModalbox from '../../../../widget/ProdGroupModalbox/ProdGroupModalbox'
import ProdGroupPlainDropdown from '../../../../widget/ProdGroupPlainDropdown'

class BodyComponent extends Component {
  static propTypes = {
    pageInstance: PropTypes.object
  }

  constructor(props) {
    super(props)

    this.state = {
      prodGroupModalboxDisplay: false
    }
  }

  showProdModalbox() {
    this.setState({ prodGroupModalboxDisplay: true })
  }

  hideProdModalbox() {
    this.setState({ prodGroupModalboxDisplay: false })
  }

  render() {
    return (
      <div ref={ wrapper => this.wrapper = wrapper }>
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

                  this.wrapper.querySelector('[data-id="search-group"]').value = thirdGroup.title
                  this.hideProdModalbox()
                }
              }
              handleCancelClick={ () => this.hideProdModalbox() }
              handleCloseClick={ () => this.hideProdModalbox() }
            />
          )
        }

        <Input
          data-id="search-kw"
          text="关键字"
        />

        {/* <Input
          data-id="search-status"
          DplComponent={ BasicDropdown }
          text="状态"
          placeholder="请选择状态"
          data={
            [
              { id: '1', name: '正常' },
              { id: '2', name: '停用' }
            ]
          }
        /> */}

        <Input
          data-id="search-group"
          text="商品分类"
          DplComponent={ ProdGroupPlainDropdown }
          handleArrowClick={ () => this.showProdModalbox() }
        />

      </div>
    )
  }
}

function onSearch({ pageInstance, apiInfo }) {
  apiInfo.data.data.keyword = pageInstance.page.querySelector('[data-id="search-kw"]').value
  // apiInfo.data.data.status = pageInstance.page.querySelector('[data-id="search-status"]').getAttribute('data-key')
  apiInfo.data.data.classification_name = pageInstance.page.querySelector('[data-id="search-group"]').value

  pageInstance.getData(1)

  // 查询后清空搜索值
  apiInfo.data.data.keyword = ''
  // apiInfo.data.data.status = ''
  apiInfo.data.data.classification_name = ''
}

export default genBasicSearch({
  BodyComponent,
  onSearch
})
