import React, { Component } from 'react'
import { BrowserRouter as Router, Route } from 'react-router-dom'
import { Login, Index } from './app'

class Routers extends Component {
  render() {
    return (
      <Router>
        <div>
          <Route exact path="/" component={ Index } />
          <Route path="/login" component={ Login } />
        </div>
      </Router>
    )
  }
}

export default Routers
