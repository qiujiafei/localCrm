const { resolve } = require('path')
const webpack = require('webpack')
const HtmlWebpackPlugin = require('html-webpack-plugin')
const autoprefixer = require('autoprefixer')
const proxy = require('./proxy')

module.exports = {
  entry: [
    'react-hot-loader/patch',
    'webpack-dev-server/client?http://localhost:8080',
    'webpack/hot/only-dev-server',
    resolve(__dirname, '../src/index.jsx')
  ],
  output: {
    filename: '[name].js'
  },
  resolve: {
    extensions: [ '.js', '.jsx' ]
  },
  performance: false,
  devtool: 'inline-source-map',
  devServer: {
    proxy: proxy,
    hot: true,
    host: '0.0.0.0',
    inline: true,
    compress: true,
    historyApiFallback: true,
    port: 8080,
    contentBase: resolve(__dirname, '../src')
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
        use: [
          {
            loader: 'pug-loader',
            options: {
              pretty: true
            }
          }
        ]
      },
      {
        test: /\.(styl|css)$/,
        use: [
          'style-loader',
          'css-loader',
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true,
              plugins: [ autoprefixer ]
            }
          },
          'stylus-loader'
        ]
      },
      {
        test: /\.(jpg|gif|svg)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '[sha512:hash:base64:8].[ext]'
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
              name: '[sha512:hash:base64:8].[ext]',
              limit: 10000
            }
          }
        ]
      }
    ]
  },
  plugins: [
    new webpack.HotModuleReplacementPlugin(),
    new webpack.NamedModulesPlugin(),
    new HtmlWebpackPlugin({
      template: resolve(__dirname, '../src/templates/index.pug')
    })
  ]
}
