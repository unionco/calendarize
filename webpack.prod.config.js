/*eslint-disable*/
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const ImageminPlugin = require('imagemin-webpack-plugin').default;
const base = require('./webpack.base.config');

base.mode = 'production';
base.optimization.minimize = true;
base.optimization.splitChunks = {
    chunks: 'all',
    minSize: 30000,
    name: 'vendor',
};

base.plugins.push(new OptimizeCSSAssetsPlugin());
base.plugins.push(new ImageminPlugin({
    test: /\.(jpe?g|png|gif|svg)$/i,
    pngquant: {
        quality: '95-100'
    }
}));

module.exports = base;