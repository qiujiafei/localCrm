const apiList = [
  'authentication',
  'authorization',
  'commodityManage',
  'commoditybatch',
  'classification',
  'commodityUnit',
  'supplier',
  'depot',
  'employee',
  'service',
  'store',
  'purchase',
  'picking',
  'damaged',
  'customerinfomation',
  'inventory',
  'damageddestroy',
  'bill',
  'ossImage',
  'finance',
  'carbasicinformation',
  'customercarstirebrand',
  'frontBridge',
  'import',
  'export'
]

const proxy = {}

apiList.forEach(api => {
  proxy['/' + api] = {
    target: 'http://127.0.0.1:3333',
    changeOrigin: true
  }
})

module.exports = proxy
