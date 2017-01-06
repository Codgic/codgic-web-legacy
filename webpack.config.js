var webpack = require('webpack');
var ExtractTextPlugin = require("extract-text-webpack-plugin");

module.exports = {
    entry: {
        'commons': './assets/commons.js',
    },
    performance: {
          hints: false
    },
    output: {
        path: __dirname + '/web/assets_webpack',
        filename: '[name].js'
    },
    module: {
        loaders: [
            { test: /\.css$/, loader: "style-loader!css-loader" },
            { test: /\.less$/, loader: "style-loader!css-loader!less-loader" },
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
                    publicPath: 'assets_webpack/'
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

    ]
}
