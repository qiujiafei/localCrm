import React, { Component } from 'react'
import PropTypes from 'prop-types'

class Input extends Component {
  static propTypes = {
    text: PropTypes.string,
    DplComponent: PropTypes.func,
    pageInstance: PropTypes.object,
    parentInstance: PropTypes.object,
    icon: PropTypes.string,
    noLabel: PropTypes.bool
  }

  static defaultProps = {
    text: ''
  }

  constructor(props) {
    super(props)
  }

  render() {
    const { text, DplComponent, pageInstance, parentInstance, icon, noLabel, ...rest } = this.props

    return (
      <div className="crm-input">
        { noLabel ? <label style={ { width: 0, paddingLeft: 0, paddingRight: 0, border: 'none' } }>&nbsp;</label> : <label>{ text }</label> }
        <div className={ DplComponent ? 'dpl' : '' }>
          { DplComponent ? <DplComponent pageInstance={ pageInstance } { ...rest } /> : <input ref={ ipt => parentInstance ? parentInstance.ipt = ipt : null } type="text" placeholder={ '请输入' + text } { ...rest } /> }
          { icon && <a className={ 'crm-icon crm-icon-' + icon }></a> }
        </div>
      </div>
    )
  }
}

export default Input
