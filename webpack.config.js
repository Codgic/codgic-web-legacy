var webpack = require('webpack');
var path = require('path');

module.exports = {
    entry: {
        'commons': './assets/commons.js',
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
        { test: /\.css$/, loader: 'style-loader!css-loader' },
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
        // split vendor js into its own file
        new webpack.optimize.CommonsChunkPlugin({
            name: 'vendor',
            minChunks: function (module, count) {
                // any required modules inside node_modules are extracted to vendor
                return (
                        module.resource &&
                        (/\.js|\.css|\.less$/.test(module.resource)) &&
                        module.resource.indexOf(
                            path.join(__dirname, 'node_modules')
                            ) === 0
                       )
            }
        }),
    ],
};
