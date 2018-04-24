import React, { Component } from 'react'
import PropTypes from 'prop-types'

function genUncontrolledModalbox(options) {

  const {
    title,
    width,
    height,
    content = 'Hello World!'
  } = options

  return class MyComponent extends Component {
    static propTypes = {
      pageInstance: PropTypes.object
    }

    constructor(props) {
      super(props)
    }

    handleClose() {
      this.props.pageInstance.hideUncontrolledModalbox()
    }

    render() {
      return (
        <div className="crm-uncontrolled-modalbox">
          <div className="inner">
            <div className="box" style={ { width, height } }>
              <header>
                <span>{ title }</span>
                <button className="crm-icon crm-icon-close" title="关闭" onClick={ e => this.handleClose(e) }></button>
              </header>
              <div className="content">
                {
                  content && content.length > 0 && (
                    <ul>
                      {
                        content.map((item, index) => {
                          return (
                            <li key={ index }>{ item }</li>
                          )
                        })
                      }
                    </ul>
                  )
                }
              </div>
            </div>
          </div>
        </div>
      )
    }
  }
}

export default genUncontrolledModalbox
