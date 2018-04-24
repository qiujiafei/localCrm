import React, { Component } from 'react'
import Button from '../widget/Button'
import Tooltip from '../widget/Tooltip/Tooltip'
import Indicator from '../widget/Indicator/Indicator'
import InputFile from '../widget/InputFile/InputFile'
import ajax from '../../lib/ajax'


function genImportPage({ title = '', list = [], url = '', templateUrl = '' }) {
  return class ImportPage extends Component {
    constructor(props) {
      super(props)

      this.state = {

        // 提示框
        toolTipText: '成功',
        toolTipType: 'normal',
        toolTipDisplay: false,

        // 加载动画
        indicatorDisplay: false
      }
    }

    showTip(text, type) {
      this.setState({ toolTipDisplay: true, toolTipText: text, toolTipType: type })
      setTimeout(() => { this.hideTip() }, 3000)
    }

    hideTip() {
      this.setState({ toolTipDisplay: false })
    }

    showIndicator() {
      this.setState({ indicatorDisplay: true })
    }

    hideIndicator() {
      this.setState({ indicatorDisplay: false })
    }

    sendData () {
      if (this.file) {
        this.showIndicator()

        this.formatFormData({
          file: this.file,
          token: localStorage.getItem('9DAYE_CRM_TOKEN')
        })

        ajax({
          method: 'POST',
          url: url,
          data: this.formatFormData({
            file: this.file,
            token: localStorage.getItem('9DAYE_CRM_TOKEN')
          })
        }).then((info) => {
          if (info.err) {
            this.showTip(info.desc, 'failed')

            if (info.goToLogin) {
              setTimeout(() => location.href = '/login', 3000)
            }
          } else {
            this.showTip('上传成功', 'success')
          }

          this.hideIndicator()

          this.file = null
          this.wrapper.querySelector('input[type="file"]').value = ''
        })
      } else {
        this.showTip('请选择文件', 'failed')
      }
    }

    formatFormData(data) {
      const formData = new FormData()

      for (const key in data) {
        if (data[key].constructor === File) {
          formData.append(key + '[]', data[key])
        } else {
          formData.append(key, data[key])
        }
      }

      return formData
    }

    downTemplate() {
      this.showIndicator()

      ajax({
        method: 'GET',
        url: templateUrl,
      }).then((info) => {
        if (info.err) {
          this.showTip(info.desc, 'failed')

          if (info.goToLogin) {
            setTimeout(() => {
              location.href = '/login'
            }, 3000)
          }
        }

        this.hideIndicator()
      }).catch(() => {
        location.href = templateUrl + '?token=' + localStorage.getItem('9DAYE_CRM_TOKEN')
        this.hideIndicator()
      })
    }

    onFileChange(file) {
      this.file = file
    }

    render() {
      return (
        <div className="crm-import-page" ref={ wrapper => this.wrapper = wrapper }>
          <Indicator
            show={ this.state.indicatorDisplay }
          />

          <Tooltip
            text={ this.state.toolTipText }
            type={ this.state.toolTipType }
            show={ this.state.toolTipDisplay }
          />

          <div className="inner">
            <div className="box">
              <header>
                <div>
                  <InputFile onFileChange={ file => this.onFileChange(file) } />
                  <Button type="assist" text="模版下载" onClick={ () => this.downTemplate() } />
                </div>
                <Button type="secondary" text="确定" onClick = { () => this.sendData() }/>
              </header>
              <footer>
                <h3>{ title }：</h3>
                <ul>
                  {
                    list.map((item, index) => {
                      return (
                        <li key={ index }>{ item }</li>
                      )
                    })
                  }
                </ul>
              </footer>
            </div>
          </div>
        </div>
      )
    }
  }
}


export default genImportPage
