import React, { Component } from 'react'
import { CircularProgress } from 'material-ui/Progress'
import PropTypes from 'prop-types'
import Button from '../widget/Button'

function generateBasic(option) {

  const {
    name = '',
    width = '500',
    height = '400',
    BodyComponent = <div></div>,
    displayFooter = true
  } = option

  return class Basic extends Component {
    static propTypes = {
      type: PropTypes.string,
      show: PropTypes.bool,
      displayIndicator: PropTypes.bool,
      handleCancelClick: PropTypes.func,
      handleSubmitClick: PropTypes.func,
      handleCloseClick: PropTypes.func,
      pageInstance: PropTypes.object
    }

    static defaultProps = {
      show: false,
      displayIndicator: false,
      type: '新增',
      handleCancelClick: () => {},
      handleSubmitClick: () => {},
      handleCloseClick: () => {}
    }

    constructor(props) {
      super(props)

      this.state = {
        show: props.show,
        displayIndicator: props.displayIndicator
      }
    }

    componentWillReceiveProps(nextProps) {
      this.setState(nextProps)
    }

    handleCancelClick(e) {
      this.props.handleCancelClick(e, this.modalbox)
    }

    handleSubmitClick(e) {
      this.props.handleSubmitClick(e, this.modalbox)
    }

    render() {
      return this.state.show ? (
        <div
          className="crm-basic-modalbox"
          ref={ modalbox => this.modalbox = modalbox }
        >
          <div className="inner">
            <div
              style={
                {
                  width: width + 'px',
                  height: height + 'px'
                }
              }
              className="box"
            >
              {
                this.state.displayIndicator ? (
                  <div className="indicator-wrapper">
                    <CircularProgress
                      classes={ {
                        root: 'indicator',
                        colorPrimary: 'indicator-primarycolor'
                      } }
                    />
                  </div>
                ) : null
              }
              <header>
                <span>{ this.props.type }{ name }</span>
                <button
                  className="crm-icon crm-icon-close"
                  title="关闭"
                  onClick={ e => this.props.handleCloseClick(e) }
                ></button>
              </header>
              <div className="box-body">
                <BodyComponent pageInstance={ this.props.pageInstance } />
              </div>
              {
                displayFooter && (
                  <footer>
                    <Button text="取消" onClick={ e => this.handleCancelClick(e) } />
                    <Button text="确定" type="primary" onClick={ e => this.handleSubmitClick(e) } />
                  </footer>
                )
              }
            </div>
          </div>
        </div>
      ) : null
    }
  }
}

export default generateBasic
