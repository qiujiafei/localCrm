import React, { Component } from 'react'
import PropTypes from 'prop-types'

import './InputFile.styl'

class InputFile extends Component {
  static propTypes = {
    onFileChange: PropTypes.func
  }

  static defaultProps = {
    onFileChange: () => {}
  }

  constructor(props) {
    super(props)
  }

  handleFileChange(e) {
    const input = e.currentTarget
    const file = input.files[0]

    if (file) {
      this.inputText.value = file.name
      this.props.onFileChange(file)
    }
  }

  render() {
    return (
      <div className="crm-input-file-full">
        <label className="label">选择文件</label>
        <div className="value">
          <input type="text" placeholder="请选择上传文件" readOnly ref={ inputText => this.inputText = inputText } />
        </div>
        <div className="desc">
          <i className="crm-icon crm-icon-category"></i>
          <label>浏览...</label>
        </div>
        <input type="file" onChange={ e => this.handleFileChange(e) } />
      </div>
    )
  }
}

export default InputFile
