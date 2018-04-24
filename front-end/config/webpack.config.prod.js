const { resolve } = require('path')
const webpack = require('webpack')
const HtmlWebpackPlugin = require('html-webpack-plugin')
const ExtractTextPlugin = require('extract-text-webpack-plugin')
const autoprefixer = require('autoprefixer')
const CopyWebpackPlugin = require('copy-webpack-plugin')

module.exports = {
  entry: {
    main: resolve(__dirname, '../src/index.jsx'),
    vendor: [
      'react',
      'react-dom',
      'lodash',
      'material-ui',
      'prop-types'
    ]
  },
  output: {
    path: resolve(__dirname, '../../commodity/web'),
    filename: 'scripts/[chunkhash].js',
  },
  resolve: {
    extensions: [ '.js', '.jsx', '.styl' ]
  },
  module: {
    rules: [
      {
        test: /\.(jsx|js)$/,
        use: [ 'babel-loader' ],
        exclude: [
          resolve(__dirname, '../node_modules')
        ]
      },
      {
        test: /\.pug$/,
        use: [ 'pug-loader' ]
      },
      {
        test: /\.(styl|css)$/,
        use: ExtractTextPlugin.extract({
          publicPath: '../',
          fallback: 'style-loader',
          use: [
            {
              loader: 'css-loader',
              options: {
                minimize: true
              }
            },
            {
              loader: 'postcss-loader',
              options: {
                sourceMap: true,
                plugins: [ autoprefixer ]
              }
            },
            'stylus-loader'
          ]
        })
      },
      {
        test: /\.(jpg|gif|svg)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: 'images/[sha512:hash:base64:8].[ext]',
            }
          }
        ]
      },
      {
        test: /\.png$/,
        use: [
          {
            loader: 'url-loader',
            options: {
              name: 'images/[sha512:hash:base64:8].[ext]',
              limit: 10000
            }
          }
        ]
      }
    ]
  },
  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: JSON.stringify('production')
      }
    }),
    new webpack.optimize.CommonsChunkPlugin({
      name: [ 'vendor', 'manifest' ]
    }),
    new webpack.optimize.UglifyJsPlugin({
      comments: false
    }),
    new HtmlWebpackPlugin({
      template: resolve(__dirname, '../src/templates/index.pug'),
      minify: {
        collapseBooleanAttributes: true,
        collapseWhitespace: true,
        removeComments: true,
        useShortDoctype: true
      }
    }),
    new CopyWebpackPlugin([
      {
        from: resolve(__dirname, '../src/favicon.ico')
      },
      {
        from: resolve(__dirname, '../src/out-dated-browser.html')
      },
      {
        from: resolve(__dirname, '../src/images/out-dated-browser-bg.png'),
        to: resolve(__dirname, '../../commodity/web/images')
      },
      {
        from: resolve(__dirname, '../src/lib/My97DatePicker'),
        to: resolve(__dirname, '../../commodity/web/lib/My97DatePicker')
      }
    ]),
    new ExtractTextPlugin({
      filename: 'stylesheets/[sha512:contenthash:base64:8].css'
    })
  ]
}
