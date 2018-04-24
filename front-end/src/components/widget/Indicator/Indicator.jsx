import React, { Component } from 'react'
import PropTypes from 'prop-types'
import { CircularProgress } from 'material-ui/Progress'

class Indicator extends Component {
  static propTypes = {
    show: PropTypes.bool.isRequired
  }

  constructor(props) {
    super(props)
    this.state = {
      show: props.show
    }
  }

  componentWillReceiveProps(nextProps) {
    this.setState(nextProps)
  }

  render() {
    return this.state.show ? (
      <div className="crm-indicator">
        <CircularProgress
          classes={ {
            root: 'crm-indicator-progress',
            colorPrimary: 'crm-indicator-primarycolor'
          } }
        />
      </div>
    ) : null
  }
}

export default Indicator
