/*
 * 选择轮胎组件
 * Author: tanglijun
 * Date: 2018-02-28
 */

import genAdvancedModalbox from '../../HOC/genAdvancedModalbox'
import columns from './columns'
import apiInfo from './apiInfo'
import Header from './Header'
import ajax from '../../../lib/ajax'

function onDelete(e, pageInstance, modalbox) {
  modalbox.showIndicator()

  ajax({
    method: 'POST',
    url: '/customercarstirebrand/del/del.do',
    data: {
      id: [ e.currentTarget.getAttribute('data-id') ]
    }
  }).then(info => {
    if (info.err) {
      pageInstance.showTip(info.desc, 'failed')

      if (info.goToLogin) {
        setTimeout(() => location.href = '/login', 3000)
      }
    } else {
      modalbox.getData()
    }
  })
}

function onEdit(e, pageInstance, modalbox) {
  const { data } = modalbox.state
  const id = e.currentTarget.getAttribute('data-id')
  const item = data.find(item => item.id == id)

  pageInstance.editId = id
  pageInstance.editDefaultName = item.brand_name

  pageInstance.showEditModalbox()
}

export default genAdvancedModalbox({
  title: '选择轮胎',
  width: '800px',
  height: 'auto',
  displayLineBtn: true,
  columns: columns.slice(),
  apiInfo,
  Header,
  onDelete,
  onEdit
})
