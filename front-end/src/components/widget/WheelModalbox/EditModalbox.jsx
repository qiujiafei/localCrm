import React, { Component } from 'react'
import PropTypes from 'prop-types'
import genBasicModalbox from '../../HOC/genBasicModalbox'

class BodyComponent extends Component {
  static propTypes = {
    pageInstance: PropTypes.object
  }

  constructor(props) {
    super(props)
  }

  componentDidMount() {
    const { pageInstance } = this.props

    this.ipt.value = pageInstance.editDefaultName
  }

  render() {
    return (
      <table>
        <tbody>
          <tr>
            <td>轮胎品牌</td>
            <td>
              <div><input ref={ ipt => this.ipt = ipt } type="text" data-id="brand_name" placeholder='输入轮胎品牌' maxLength="30" /></div>
            </td>
          </tr>
        </tbody>
      </table>
    )
  }
}

export default genBasicModalbox({
  name: '轮胎品牌',
  width: '500',
  height: '200',
  BodyComponent: BodyComponent
})
