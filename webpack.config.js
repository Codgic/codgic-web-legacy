var webpack = require('webpack');
var path = require('path');

module.exports = {
    entry: {
        'general': './assets/pages/general.js',
        'problempage': './assets/pages/problempage.js',
        'sourcecode': './assets/pages/sourcecode.js',
        'contestpage': './assets/pages/contestpage.js',
    },
    performance: {
        hints: false
    },
    output: {
        path: __dirname + '/web/assets_webpack',
        filename: '[name].js',
        publicPath: 'assets_webpack/'

    },
    module: {
        loaders: [
        { test: /\.css$/, loader: 'style-loader!css-loader?minimize' },
        { test: /\.less$/, loader: 'style-loader!css-loader!less-loader' },
        {
            test: /\.js$/,
            exclude: /(node_modules|bower_components)/,
            loader: 'babel-loader?presets[]=es2015'
        },
        {
            test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
            loader: 'file-loader',
        },
        {
            test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
            loader: 'url-loader',
            query: {
                limit: 10000,
            }
        }
        ]
    },
    plugins: [
        new webpack.optimize.UglifyJsPlugin({
            compress: {
                warnings: false
            }
        }),
        new webpack.optimize.CommonsChunkPlugin({ 
            name: 'commons',
            filename: 'commons.js',
            chunks: ["general", "problempage"]
        }),
    ],
};
