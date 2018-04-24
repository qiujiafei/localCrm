import React, { Component } from 'react'

import welPng from './wel.png'

class Deny extends Component {
  constructor(props) {
    super(props)
  }

  render() {
    return (
      <div style={ { textAlign: 'center', paddingTop: '200px' } }>
        <img src={ welPng } />
      </div>
    )
  }
}

export default Deny
