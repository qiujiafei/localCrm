// https://github.com/react-component/pagination

import React, { Component } from 'react'
import RcPagination from 'rc-pagination'

import './Pagination.styl'

class Pagination extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <div className="crm-pagination">
        <RcPagination { ...this.props } />
      </div>
    )
  }
}

export default Pagination
