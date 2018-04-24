if (process.env.NODE_ENV !== 'production') {
  require('./templates/index.pug')
}

import React from 'react'
import ReactDOM from 'react-dom'

// AppContainer is a necessary wrapper component for HMR
import { AppContainer } from 'react-hot-loader'

import Routers from './routers'

import 'rc-pagination/assets/index.css'
import './stylesheets/crm.styl'

const render = Component => {
  ReactDOM.render(
    <AppContainer>
      <Component />
    </AppContainer>,
    document.getElementById('root')
  )
}

render(Routers)

// Hot Module Replacement API
if (module.hot) {
  module.hot.accept([ './routers' ], () => render(Routers))
}
