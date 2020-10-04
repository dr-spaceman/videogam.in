/* eslint-disable indent */
const { merge } = require('webpack-merge');
const webpack = require('webpack'); // to access built-in plugins
const dotenv = require('dotenv');
const common = require('./webpack.common.js');

const env = dotenv.config().parsed;

// reduce it to a nice object, the same as before
const envKeys = Object.keys(env).reduce((prev, next) => {
    prev[`process.env.${next}`] = JSON.stringify(env[next]);
    return prev;
}, {});

module.exports = merge(common, {
    mode: 'development',
    devtool: 'inline-source-map',
    plugins: [
        new webpack.DefinePlugin(envKeys),
    ],
});
