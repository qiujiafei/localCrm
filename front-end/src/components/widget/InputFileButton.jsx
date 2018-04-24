import React, { Component } from 'react'
import PropTypes from 'prop-types'
import Button from './Button'
import genPreviewModalbox from '../HOC/genPreviewModalbox'
import upload from '../../lib/upload'

class InputFile extends Component {
  static propTypes = {
    name: PropTypes.string,
    size: PropTypes.string,
    text: PropTypes.string,
    type: PropTypes.string,
    pageInstance: PropTypes.object
  }

  constructor(props) {
    super(props)

    const { pageInstance, name, ...rest } = this.props

    this.pageInstance = pageInstance
    this.rest = rest
    this.name = name

    this.state = {
      previewModalboxDisplay: false,
      previewImgUrl: ''
    }
  }

  getExtName(fileName) {
    return fileName.match(/\.(.*)$/)[1]
  }

  onFileChange(e) {
    const element = e.currentTarget
    const file = element.files[0]
    const preViewElement = this.wrapper.querySelectorAll('.crm-button')[1]

    if (file.size / 1024 / 1024 > 2) {
      this.pageInstance.showTip('图片大小不能大于 2m', 'failed')
    } else {
      if (file) {
        file.extName = this.getExtName(file.name)
        this.uploadFile(file).then(info => {
          if (info) {
            preViewElement.style.display = 'inline-block'
            preViewElement.setAttribute('data-img', info.url)
            this.inputFile.setAttribute('data-img', info.url)
            this.inputFile.setAttribute('data-name', info.filename)
          }
        })
      }
    }
  }

  uploadFile(file) {
    this.pageInstance.showModalBoxIndicator()

    return upload(file)
      .then(info => {
        this.pageInstance.hideModalBoxIndicator()

        if (info.err) {
          this.pageInstance.showTip(info.desc, 'failed')

          if (info.goToLogin) {
            setTimeout(() => { location.href = '/login' }, 3000)
          }
        } else {
          return info
        }
      })

  }

  getPreviewResult(file, callback) {
    const reader = new FileReader()

    reader.onload = e => {
      callback(e.target.result)
    }
    reader.readAsDataURL(file)
  }

  render() {
    const Modalbox = genPreviewModalbox({
      title: '预览' + this.name
    })

    return (
      <div className="crm-input-file-button" ref={ wrapper => this.wrapper = wrapper }>
        <div>
          <input
            ref={ inputFile => this.inputFile = inputFile }
            type="file"
            title="选择文件"
            accept="image/*"
            onChange={ e => this.onFileChange(e) }
            { ...this.rest }
          />
          <Button text="选择文件" onClick={ () => { this.inputFile.click() } } />
        </div>
        <Button
          style={ { display: 'none' } }
          text="预览"
          onClick={
            (e) => {
              this.setState({ modalboxDisplay: true, previewImgUrl: e.currentTarget.getAttribute('data-img') })
            }
          }
        />
        {
          this.state.modalboxDisplay && (
            <Modalbox
              imgUrl={ this.state.previewImgUrl }
              onCloseClick={ () => this.setState({ modalboxDisplay: false }) }
            />
          )
        }
      </div>
    )
  }
}

export default InputFile
