import React, { Component } from 'react'
import Pagination from './Pagination'

import 'rc-pagination/assets/index.css'

class PaginationTest extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <Pagination total={ 100 } />
    )
  }
}

export default PaginationTest
