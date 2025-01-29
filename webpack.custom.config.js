const fs = require('fs');
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');

// Function to get features for a given environment
const getFeatures = (env) => {
    const envPath = path.resolve(__dirname, 'src', env);
    return fs.readdirSync(envPath).filter(file =>
        fs.statSync(path.join(envPath, file)).isDirectory() && file !== 'gutenberg'
    );
};

// Get features for admin and frontend
const adminFeatures = getFeatures('admin');
const frontendFeatures = getFeatures('frontend');

// Dynamically create entry points
const entry = {};
adminFeatures.forEach(feature => {
    entry[`admin/${feature}`] = `./src/admin/${feature}/index.js`;
});
frontendFeatures.forEach(feature => {
    entry[`frontend/${feature}`] = `./src/frontend/${feature}/index.js`;
});

module.exports = (env, argv) => {

    const isProduction = argv.mode === 'production';

    return {

        entry,
        output: {
            path: path.resolve(__dirname, 'build'),
            filename: '[name]/index.js'
        },
        plugins: [
            new MiniCssExtractPlugin({
                filename: '[name]/index.css'
            })
        ],
        module: {
            rules: [
                {
                    test: /\.(js|jsx)$/,
                    exclude: /node_modules/,
                    use: {
                        loader: 'babel-loader',
                        options: {
                            presets: ['@babel/preset-env', '@babel/preset-react']
                        }
                    }
                },
                {
                    test: /\.scss$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        'css-loader',
                        'sass-loader'
                    ]
                }
            ]
        },
        resolve: {
            extensions: ['.js', '.jsx', '.json'],
            alias: {
                // admin
                '@admin': path.resolve(__dirname, 'src/admin'),                
                '@settings': path.resolve(__dirname, 'src/admin/settings-page'),
                '@addOnsManager': path.resolve(__dirname, 'src/admin/add-ons-manager'),
                '@orderDetails': path.resolve(__dirname, 'src/admin/order-details'),

                // frontend
                '@frontend': path.resolve(__dirname, 'src/frontend'),
                '@olenaStore': path.resolve(__dirname, 'src/frontend/olena-store'),
                '@singleItemButton': path.resolve(__dirname, 'src/frontend/single-item-button'),
                '@cartWidget': path.resolve(__dirname, 'src/frontend/cart-widget'),
            }
        },
        optimization: {
            minimize: isProduction,
            minimizer: [
                new TerserPlugin(),
                new CssMinimizerPlugin(),
            ],
            splitChunks: {
                cacheGroups: {
                    defaultVendors: {
                        test: /[\\/]node_modules[\\/]/,
                        name: 'dependencies/vendors',
                        chunks: 'all',
                        enforce: true,
                    },
                    common: {
                        name: 'dependencies/common',
                        minChunks: 2,
                        chunks: 'all',
                        priority: -20,
                        reuseExistingChunk: true,
                        enforce: true,
                    },
                },
            },
        },
        devtool: isProduction ? false : 'source-map',
        watchOptions: {
            ignored: /node_modules/,
            aggregateTimeout: 300,
            poll: 1000,
        },
    }
};
