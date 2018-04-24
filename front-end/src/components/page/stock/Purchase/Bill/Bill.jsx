import columns from './columns'
import Header from './Header'
import Footer from './Footer'
import Calculation from './Calculation'
import ProdModalbox from './ProdModalbox/ProdModalbox'
import DepotModalbox from '../../../../widget/DepotModalbox/DepotModalbox'
import genBillPage from '../../../../HOC/genBillPage'

export default genBillPage({
  columns,
  Header,
  Footer,
  Calculation,
  ProdModalbox,
  DepotModalbox
})
