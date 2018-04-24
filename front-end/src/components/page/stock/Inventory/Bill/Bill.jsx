import columns from './columns'
import Header from './Header'
import Footer from './Footer'
import util from '../../../../../lib/util'
import DepotModalbox from '../../../../widget/DepotModalbox/DepotModalbox'
import ProdModalbox from '../../../../widget/ProdModalbox/ProdModalbox'
import genBillPage from '../../../../HOC/genBillPage'

function onInputClick(e, column, pageInstance) {
  const index = e.currentTarget.getAttribute('data-index') - 1
  const id = e.currentTarget.getAttribute('data-api-id')
  const stockElements = pageInstance.billPage.querySelectorAll('[data-api-id="stock"]')
  const resultElements = pageInstance.billPage.querySelectorAll('[data-api-id="result"]')
  const totalElements = pageInstance.billPage.querySelectorAll('[data-api-id="' + id + '"]')
  const totalElement = pageInstance.billPage.querySelector('[data-id="total-' + id + '"]')
  const totalResult = pageInstance.billPage.querySelector('[data-id="total-result"]')

  e.currentTarget.value = util.formateToDecimal(e.currentTarget.value)

  if (e.currentTarget.value != '') {
    resultElements[index].value = (Number(e.currentTarget.value) - Number(stockElements[index].value)).toFixed(2)
    totalElement.innerHTML = calcTotal()
    totalResult.innerHTML = calcTotalResult()
    pageInstance.apiData[index][id] = e.currentTarget.value
    pageInstance.apiData[index]['result'] = resultElements[index].value
  } else {
    resultElements[index].value = ''
    totalElement.innerHTML = ''
    totalResult.innerHTML = ''
    pageInstance.apiData[index][id] = ''
    pageInstance.apiData[index]['result'] = ''
  }

  function calcTotal() {
    let total = 0

    totalElements.forEach(resultElement => {
      total += Number(resultElement.value)
    })

    return total.toFixed(2)
  }

  function calcTotalResult() {
    let total = 0

    resultElements.forEach(resultElement => {
      total += Number(resultElement.value)
    })

    return total.toFixed(2)
  }
}

function onInputBlur(e) {
  e.currentTarget.value = Number(e.currentTarget.value).toFixed(2)
}

export default genBillPage({
  columns,
  Header,
  Footer,
  DepotModalbox,
  ProdModalbox,
  onInputClick,
  onInputBlur,
  noDefaultData: true,
  displayBatchBtn: false
})
