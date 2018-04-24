import columns from './columns'
import Header from './Header'
import Footer from './Footer'
import Calculation from './Calculation'
import DepotModalbox from '../../../../widget/DepotModalbox/DepotModalbox'
import ProdModalbox from '../../../../widget/ProdModalbox/ProdModalbox'
import genBillPage from '../../../../HOC/genBillPage'

export default genBillPage({
  columns,
  Header,
  Footer,
  Calculation,
  DepotModalbox,
  ProdModalbox
})
