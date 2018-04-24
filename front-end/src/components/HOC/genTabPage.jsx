import React, { Component } from 'react'
import PropTypes from 'prop-types'

function genTabPage(tabs = []) {
  return class TabPage extends Component {

    static propTypes = {
      mapInfo: PropTypes.object,
      transferData: PropTypes.func
    }

    constructor(props) {
      super(props)

      this.state = {
        tabs: tabs,
        panels: [],
        Panel: () => <div></div>
      }
    }

    componentDidMount() {

      // 初始化选中第一个 tab
      const tab = this.wrapper.querySelectorAll('.tab')[0]

      tab.className = 'tab on'

      this.setState({ Panel: this.props.mapInfo[tab.title].component })
      // this.wrapper.querySelectorAll('.panels > div')[0].className += ' on'
    }

    handleTabClick(e) {
      e.preventDefault()

      const prevTab = this.wrapper.querySelector('.tab.on')
      // const prevPanel = this.wrapper.querySelector('.panels > div.on')
      const target = e.currentTarget
      // const index = _.indexOf(tabs, e.currentTarget.title)

      if (prevTab) {
        prevTab.className = 'tab'
        // prevPanel.className = prevPanel.className.replace(/\s*on\s*/, '')
      }

      target.className = 'tab on'

      this.setState({ Panel: this.props.mapInfo[target.title].component })
      // this.wrapper.querySelectorAll('.panels > div')[index].className += ' on'
    }

    render() {
      const Panel = this.state.Panel

      return (
        <div className="crm-tab-page" ref={ wrapper => this.wrapper = wrapper }>
          <header>
            <nav className="tab-wrapper">
              {
                this.state.tabs.map(tab => {
                  return (
                    <a
                      className="tab"
                      data-id={ tab }
                      title={ tab }
                      key={ tab }
                      onClick={ e => this.handleTabClick(e) }
                    >{ tab }</a>
                  )
                })
              }
            </nav>
          </header>
          <Panel transferData={ data => this.props.transferData(data) } mapInfo={ this.props.mapInfo } { ...this.props } />

          {/* {
            <div className="panels">
              {
                this.state.tabs.map((tab, index) => {
                  const Panel = this.props.mapInfo[tab].component

                  return (
                    <div className="panel" key={ index }>
                      <Panel transferData={ data => this.props.transferData(data) } mapInfo={ this.props.mapInfo } { ...this.props } />
                    </div>
                  )
                })
              }
            </div>
          } */}
        </div>
      )
    }
  }
}

export default genTabPage
