/*
 * 按钮组 HOC 组件
 * Author: tanglijun
 * Date: 2018-02-10
 */

import React, { Component } from 'react'
import PropTypes from 'prop-types'

function genRadioButtonGroup(options) {

  const {
    // 按钮组名称
    name = 'btns',
    // 按钮信息列表 array
    buttons = [
      {
        id: '1',
        text: '按钮1'
      },
      {
        id: '2',
        text: '按钮2'
      },
      {
        id: '3',
        text: '按钮3'
      }
    ]
  } = options

  return class RadioButtonGroup extends Component {
    static propTypes = {
      onItemClick: PropTypes.func
    }

    static defaultProps = {
      onItemClick: () => {}
    }

    constructor(props) {
      super(props)
    }

    onItemClick(e) {
      const clickedLabel = e.currentTarget
      const selectedLabel = this.wrapper.querySelector('.on')

      // 点击的 label 为选中 label，不作处理
      if (clickedLabel === selectedLabel) {
        return
      }

      clickedLabel.className = 'on'

      selectedLabel && selectedLabel.removeAttribute('class')

      this.props.onItemClick(e.currentTarget.getAttribute('data-id'))
    }

    render() {
      return (
        <div className="crm-radio-button-group" ref={ wrapper => this.wrapper = wrapper }>
          {
            buttons.map((button, index) => {
              const id = name + '-' + button.id

              if (button.selected) {
                return (
                  <label onClick={ e => this.onItemClick(e) } className="on" key={ index } data-id={ id } title={ button.text }>
                    <span>{ button.text }</span>
                  </label>
                )
              } else {
                return (
                  <label onClick={ e => this.onItemClick(e) } key={ index } data-id={ id } title={ button.text }>
                    <span>{ button.text }</span>
                  </label>
                )
              }
            })
          }
        </div>
      )
    }
  }
}

export default genRadioButtonGroup
