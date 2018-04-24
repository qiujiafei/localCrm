import React, { Component } from 'react'
import PropTypes from 'prop-types'
import Button from '../widget/Button'

function genBasic(options) {
  const {
    BodyComponent,
    onSearch
  } = options

  return class Search extends Component {
    static propTypes = {
      pageInstance: PropTypes.object,
      apiInfo: PropTypes.object
    }

    constructor(props) {
      super(props)
    }

    handleSearch() {
      onSearch(this.props)
    }

    render() {
      return (
        <div className="search-wrapper" ref={ wrapper => this.wrapper = wrapper }>
          <BodyComponent pageInstance={ this.props.pageInstance } />
          <Button text="查询" type="secondary" onClick={ e => this.handleSearch(e) } />
        </div>
      )
    }
  }
}

export default genBasic
