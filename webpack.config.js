const path    = require('path');
const webpack = require('webpack');

const MiniCssExtractPlugin    = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const TerserPlugin            = require('terser-webpack-plugin');

const ManifestPlugin = require('webpack-manifest-plugin');
const AssetsManifest = require('webpack-assets-manifest');

const ChunkHash = require('webpack-chunk-hash');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

const isProduction = process.env.NODE_ENV === 'production';
const useSourceMap = !isProduction;

const cssLoader = {
    loader: 'css-loader',
    options: {
        sourceMap: useSourceMap
    },
};

const sassLoader = {
    loader: 'sass-loader',
    options: {
        sourceMap: true,
    },
};

const resolveUrlLoader = {
    loader: 'resolve-url-loader',
    options: {
        sourceMap: useSourceMap,
    },
};

const CssExtractLoader = {
    loader: MiniCssExtractPlugin.loader,
};

const webpackConfig = {
    mode: isProduction ? 'production' : 'development',
    entry: {
        base: './assets/js/base.js',
        home: './assets/js/home.js',
        event_party: './assets/js/event_party.js',
        vk_auth: './assets/js/vk_auth.js',
        fill_user: './assets/js/fill_user.js',
    },
    output: {
        path: path.resolve(__dirname, 'public', 'build'),
        filename: '[name].[chunkhash:6].js',
        publicPath: '/build/',
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        cacheDirectory: true
                    }
                }
            },
            {
                test: /\.css$/,
                use: [
                    CssExtractLoader,
                    cssLoader,
                ]
            },
            {
                test: /\.scss$/,
                use: [
                    CssExtractLoader,
                    cssLoader,
                    resolveUrlLoader,
                    sassLoader
                ]
            },
            {
                test: /\.(png|jpg|jpeg|gif|ico|svg)$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[name]-[hash:6].[ext]'
                        }
                    }
                ]
            },
            {
                test: /\.(woff|woff2|eot|ttf|otf)$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[name]-[hash:6].[ext]'
                        }
                    }
                ]
            },
        ]
    },
    plugins: [
        new webpack.ProvidePlugin({
            jQuery: 'jquery',
            $: 'jquery',
            'window.jQuery': 'jquery',
        }),

        // to {output}/static
        new CopyWebpackPlugin([
            { from: './assets/static', to: 'static' }
        ]),

        new MiniCssExtractPlugin({
            filename: '[name].[contenthash:6].css',
        }),

        new ManifestPlugin({
            writeToFileEmit: true,
            basePath: 'build/'
        }),

        // create manifest.json with array of asset chunks
        // new AssetsManifest({
        //     entrypoints: true,
        //     publicPath: true,
        //     transform: assets => assets.entrypoints
        // }),

        // allows [chunkhash]
        new ChunkHash(),

        new CleanWebpackPlugin(),
    ],
    devtool: useSourceMap ? 'inline-source-map' : false,
    optimization: {
        moduleIds: isProduction ? 'hashed' : 'named',
        splitChunks: {
            chunks: "all",
            name: 'vendor',
            minChunks: 2,
        }
    },
};

if (isProduction) {
    webpackConfig.optimization.minimizer = [new TerserPlugin(), new OptimizeCSSAssetsPlugin()];

    webpackConfig.plugins.push(
        new webpack.DefinePlugin({
            'process.env.NODE_ENV': JSON.stringify('production')
        })
    );
}

module.exports = webpackConfig;
