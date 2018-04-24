import React, { Component } from 'react'
import Indicator from '../../widget/Indicator/Indicator'
import genBasicModalbox from '../../HOC/genBasicModalbox'
import ajax from '../../../lib/ajax'

import './TypeOfCarModalbox.styl'

class BodyComponent extends Component {
  constructor(props) {
    super(props)

    this.state = {
      alphabet: [],
      brands: [],
      types: [],
      modals: [],

      // 加载动画
      indicatorBrandDisplay: false,
      indicatorTypeDisplay: false,
      indicatorModalDisplay: false
    }
  }

  onClick(e) {
    const selectedItem = this.wrapper.querySelector('.item.alphabet li.on')
    const charId = e.currentTarget.getAttribute('data-id')

    if (selectedItem) {
      selectedItem.removeAttribute('class')
    }

    e.currentTarget.className = 'on'

    this.getBrand(charId)
  }

  onBrandClick(e) {
    const selectedItem = this.wrapper.querySelector('.item.brand li.on')
    const brandId = e.currentTarget.getAttribute('data-id')
    const alphabetId = e.currentTarget.getAttribute('data-alphabet-id')

    if (selectedItem) {
      selectedItem.removeAttribute('class')
    }

    e.currentTarget.className = 'on'

    this.getType(alphabetId, brandId)
  }

  onTypeClick(e) {
    const selectedItem = this.wrapper.querySelector('.item.type li.on')
    const typeId = e.currentTarget.getAttribute('data-id')
    const brandId = e.currentTarget.getAttribute('data-brand-id')
    const alphabetId = e.currentTarget.getAttribute('data-alphabet-id')

    if (selectedItem) {
      selectedItem.removeAttribute('class')
    }

    e.currentTarget.className = 'on'

    this.getModal(alphabetId, brandId, typeId)
  }

  onModalClick(e) {
    const selectedItem = this.wrapper.querySelector('.item.modal li.on')

    if (selectedItem) {
      selectedItem.removeAttribute('class')
    }

    e.currentTarget.className = 'on'
  }

  componentDidMount() {
    this.getAlphabet()
  }

  showBrandIndicator() {
    this.setState({ indicatorBrandDisplay: true })
  }

  hideBrandIndicator() {
    this.setState({ indicatorBrandDisplay: false })
  }

  showTypeIndicator() {
    this.setState({ indicatorTypeDisplay: true })
  }

  hideTypeIndicator() {
    this.setState({ indicatorTypeDisplay: false })
  }

  showModalIndicator() {
    this.setState({ indicatorModalDisplay: true })
  }

  hideModalIndicator() {
    this.setState({ indicatorModalDisplay: false })
  }

  getModal(alphabet_id, brand_id, type_id) {
    this.showModalIndicator()

    ajax({
      method: 'GET',
      url: '/carbasicinformation/get/getCarStyleHome.do',
      data: {
        alphabet_id,
        brand_id,
        type_id
      }
    }).then(info => {
      this.setState({ modals: info.data.car_style_home })
      this.hideModalIndicator()
    })
  }

  getType(alphabet_id, brand_id) {
    this.showTypeIndicator()

    ajax({
      method: 'GET',
      url: '/carbasicinformation/get/getCarTypeHome.do',
      data: {
        alphabet_id,
        brand_id
      }
    }).then(info => {
      this.setState({ types: info.data.car_type_home })
      this.hideTypeIndicator()
    })
  }

  getBrand(alphabet_id) {
    this.showBrandIndicator()

    ajax({
      method: 'GET',
      url: '/carbasicinformation/get/getCarBrandHome.do',
      data: {
        alphabet_id
      }
    }).then(info => {
      this.setState({ brands: info.data.car_brand_home })
      this.hideBrandIndicator()
    })
  }

  getAlphabet() {
    ajax({
      method: 'GET',
      url: '/carbasicinformation/get/getCarAlphabetHome.do'
    }).then(info => {
      this.setState({ alphabet: info.data })
    })
  }

  render() {
    return (
      <div className="type-of-car-modalbox" ref={ wrapper => this.wrapper = wrapper }>
        <div className="item alphabet">
          <header>字母</header>
          {
            this.state.alphabet.length > 0 && (
              <ul>
                {
                  this.state.alphabet.map((char, index) => {
                    return (
                      <li
                        key={ index }
                        data-id={ char.id }
                        data-name={ char.name }
                        title={ char.name }
                        onClick={ (e) => this.onClick(e) }>
                        { char.name }
                      </li>
                    )
                  })
                }
              </ul>
            )
          }
        </div>

        <div className="item brand">
          <header>品牌</header>
          { this.state.brands.length > 0 ? (
            <ul>
              {
                this.state.brands.map((item, index) => {
                  return (
                    <li
                      key={ index }
                      data-id={ item.id }
                      data-alphabet-id={ item.alphabet_id }
                      data-alphabet-name={ item.alphabet_name }
                      onClick={ e => this.onBrandClick(e) }
                    >
                      { item.brand_name }
                    </li>
                  )
                })
              }
            </ul>
          ) : '请选择字母' }
          <Indicator show={ this.state.indicatorBrandDisplay } />
        </div>

        <div className="item type">
          <header>型号</header>
          { this.state.types.length > 0 ? (
            <ul>
              {
                this.state.types.map((item, index) => {
                  return (
                    <li
                      key={ index }
                      data-id={ item.id }
                      data-brand-id={ item.brand_id }
                      data-alphabet-id={ item.alphabet_id }
                      onClick={ e => this.onTypeClick(e) }
                    >
                      { item.car_type_name }
                    </li>
                  )
                })
              }
            </ul>
          ) : '请选择品牌' }
          <Indicator show={ this.state.indicatorTypeDisplay } />
        </div>

        <div className="item modal">
          <header>款式</header>
          { this.state.modals.length > 0 ? (
            <ul>
              {
                this.state.modals.map((item, index) => {
                  const styleName = item.year + '款' + item.style_name

                  return (
                    <li
                      key={ index }
                      data-id={ item.id }
                      data-style-name={ styleName }
                      onClick={ e => this.onModalClick(e) }
                    >
                      { styleName }
                    </li>
                  )
                })
              }
            </ul>
          ) : '请选择型号' }
          <Indicator show={ this.state.indicatorModalDisplay } />
        </div>
      </div>
    )
  }
}

export default genBasicModalbox({
  name: '品牌车型',
  width: '1000',
  height: '600',
  BodyComponent
})
