import genBasicPage from '../../../../HOC/genBasicPage'
import columns from './columns'
import apiInfo from './apiInfo'
import menus from './menus'
import Modalbox from './Modalbox'

function genTableConfig(pageInstance, apiInfo) {
  return {
    pageSize: pageInstance.state.data.length || 15,
    getTrProps: (state, rows) => {
      return {
        style: {
          cursor: 'pointer'
        },
        onClick: () => {
          const depth = Number(rows.original.depth) + 1

          if (!rows.original.end) {
            const itemInData = pageInstance.state.data.find(item => item.id == rows.original.id)
            const index = pageInstance.state.data.indexOf(itemInData)
            const originalData = [ ...pageInstance.state.data ]

            if (!itemInData.open) {
              itemInData.open = true

              pageInstance.ajaxRequest({
                method: 'GET',
                url: apiInfo.data.url,
                data: {
                  parent_id: rows.original.id,
                  depth
                },
                afterRequest: info => {
                  const data = pageInstance.formatData(info.data).map(item => {
                    if (depth === 3) {
                      item.end = true
                    }
                    item.sub = true
                    return item
                  })

                  originalData.splice(index + 1, 0, ...data)
                  pageInstance.setState({ data: originalData })

                  if (!pageInstance.tmpStore) {
                    pageInstance.tmpStore = {}
                  }

                  pageInstance.tmpStore[rows.original.id] = data.length

                  if (rows.original.parent_id != '-1') {
                    pageInstance.tmpStore[rows.original.parent_id] += data.length
                  }
                }
              })
            } else {
              originalData.splice(index + 1, pageInstance.tmpStore[itemInData.id])
              itemInData.open = false
              pageInstance.setState({ data: originalData })
            }
          }
        }
      }
    }
  }
}

function onModalboxSubmitClick(data) {
  const newData = {}
  const except = [
    'parent_name'
  ].join('')

  for (var key in data) {
    if (except.indexOf(key) === -1) {
      newData[key] = data[key]
    }
  }

  return newData
}

export default genBasicPage({
  displayOrderNum: false,
  displayBatchBtn: false,
  displayPagination: false,
  genTableConfig,
  columns,
  apiInfo,
  menus,
  Modalbox,
  onModalboxSubmitClick
})
