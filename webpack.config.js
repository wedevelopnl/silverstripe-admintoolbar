const path = require('path');
const Webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const ESLintPlugin = require('eslint-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const ESLintConfig = require('./.eslintrc');

const isDevMode = process.env.NODE_ENV === 'development';

const plugins = [
  new FixStyleOnlyEntriesPlugin(),
  new MiniCssExtractPlugin({
    filename: ({ chunk }) => `${chunk.name.replace('/js/', '/css/')}.css`,
  }),
  new ESLintPlugin({
    overrideConfig: ESLintConfig
  }),
];

module.exports = {
  devtool: isDevMode ? 'source-map' : false,
  mode: isDevMode ? 'development' : 'production',
  watchOptions: {
    ignored: /node_modules/,
  },
  entry: {
    'app': './client/src/js/app.js',
    'flush-cache-button': './client/src/js/flush-cache-button.js',
    'queries-button': './client/src/js/queries-button.js',
    'queries-toggle': './client/src/js/queries-toggle.js',
    'timing-toggle': './client/src/js/timing-toggle.js',
    'timing-button': './client/src/js/timing-button.js',
    'main': './client/src/sass/main.scss',
  },
  optimization: {
    minimize: !isDevMode,
    minimizer: [
      new TerserPlugin(),
      new CssMinimizerPlugin()
    ],
  },
  output: {
    path: path.resolve(__dirname, './client/dist')
  },
  module: {
    rules: [
      {
        test: /\.(sa|sc|c)ss$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              publicPath: '',
            },
          },
          {
            loader: 'css-loader',
            options: {
              sourceMap: isDevMode,
            }
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: isDevMode,
            }
          },
          {
            loader: 'sass-loader',
            options: {
              implementation: isDevMode ? require('sass-embedded') : require('sass'),
              sourceMap: isDevMode,
              sassOptions: {
                outputStyle: "compressed"
              }
            }
          },
        ],
      },
      {
        test: /\.m?js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env'],
            plugins: [
              '@babel/plugin-transform-runtime',
            ],
          },
        },
      },
    ],
  },
  plugins
};
