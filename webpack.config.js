const path = require('path');
const glob = require('glob');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const IgnoreEmitPlugin = require('ignore-emit-webpack-plugin');

module.exports = [
    {
        entry: glob.sync('./Assets/sass/*.scss').reduce(function(obj, el){
            obj[path.parse(el).name] = el;
            return obj
        },{}),
        output: {
          path: path.resolve(__dirname, 'Dist'),
        },
        module: {
            rules: [
                {
                    test: /\.scss$/i,
                    use: [
                        MiniCssExtractPlugin.loader,
                        "css-loader",
                        "sass-loader",
                    ],
                },
            ],
        },
        plugins: [
            new MiniCssExtractPlugin(),
            new IgnoreEmitPlugin(/\.js(\?.*)?$/i,),
        ]
    },
    {
        entry: glob.sync('./Assets/js/*.js').reduce(function(obj, el){
            obj[path.parse(el).name] = el;
            return obj
        },{}),
        output: {
            path: path.resolve(__dirname, 'Dist'),
            filename: "[name].js"
        },
        optimization: {
            minimizer: [
                new UglifyJsPlugin({
                    test: /\.js(\?.*)?$/i,
                    uglifyOptions: {
                        mangle: true,
                    }
                })
            ],
        },
        plugins: [
            new CleanWebpackPlugin(),
        ]
    }
];
