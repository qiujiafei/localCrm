import React, { Component } from 'react'
import PropTypes from 'prop-types'

class CommonDropdown extends Component {
  static propTypes = {
    data: PropTypes.array,
    defaultKey: PropTypes.string,
    onItemClick: PropTypes.func,
    onArrowClick: PropTypes.func,
    pageInstance: PropTypes.object
  }

  static defaultProps = {
    data: [],
    onItemClick: () => {},
    onArrowClick: () => {}
  }

  constructor(props) {
    super(props)

    const { data, pageInstance, ...rest } = props

    this.state = {
      data: data
    }

    this.show = this.show.bind(this)
    this.hide = this.hide.bind(this)
    this.rest = rest
    this.pageInstance = pageInstance
  }

  show() {
    this.list.className += ' show'
  }

  hide() {
    this.list.className = this.list.className.replace(/\s?show\s?/, '')
  }

  onItemClick(e, callback) {
    const key = e.currentTarget.getAttribute('data-key')
    const name = e.currentTarget.getAttribute('data-name')

    this.ipt.setAttribute('data-key', key)
    this.ipt.value = name
    this.hide()

    callback(key, name)
  }

  onArrowClick(e, callback) {
    e.preventDefault()

    if (this.list.className.match('show')) {
      this.hide()
    } else {
      this.show()
    }

    callback()
  }

  componentDidMount() {
    window.addEventListener('click', this.hide)
  }

  componentWillUnmount() {
    window.removeEventListener('click', this.hide)
  }

  componentWillReceiveProps(nextProps) {
    this.setState(nextProps)
  }

  render() {

    const { onArrowClick, onItemClick, ...rest } = this.rest

    return (
      <div className="crm-dpl" onClick={ e => e.stopPropagation() }>
        <div className="value">
          <input type="text" placeholder="--请选择--" readOnly ref={ ipt => this.ipt = ipt } { ...rest } />
          <a className="fa fa-angle-down" onClick={ e => this.onArrowClick(e, onArrowClick) }></a>
        </div>
        <div className="list" ref={ list => this.list = list }>
          {
            this.state.data.length > 0 && (
              <ul>
                {
                  this.state.data.map((item, index) => {
                    return (
                      <li data-key={ item.id } data-name={ item.name } key={ index } onClick={ e => this.onItemClick(e, onItemClick) }>
                        { item.name }
                      </li>
                    )
                  })
                }
              </ul>
            )
          }
        </div>
      </div>
    )
  }
}

export default CommonDropdown
