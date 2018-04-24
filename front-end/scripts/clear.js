const rimraf = require('rimraf')
const path = require('path')

const willRemoveFiles = [
  'images',
  'lib',
  'scripts',
  'stylesheets',
  'favicon.ico',
  'index.html',
  'out-dated-browser.html'
]

willRemoveFiles.forEach(file => {
  rimraf.sync(path.resolve(__dirname, '../../commodity/web', file))
})
