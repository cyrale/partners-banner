const path = require( 'path' );

const CleanWebpackPlugin = require( 'clean-webpack-plugin' );

const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );
const UglifyJsPlugin = require( 'uglifyjs-webpack-plugin' );

module.exports = ( env, options ) => {
	const mode = options.mode || process.env.NODE_ENV || 'production';

	const optimization = {};

	if ( mode === 'production' ) {
		optimization.minimizer = [
			new UglifyJsPlugin( {
				cache: true,
				parallel: true,
				sourceMap: true,
				uglifyOptions: {
					compress: true,
				},
			} ),
		];
	}

	return {
		mode: mode,
		devtool: 'source-map',
		context: path.resolve( __dirname ),
		entry: {
			admin: './src/admin.js',
			app: './src/app.js',
		},
		optimization: optimization,
		plugins: [
			new CleanWebpackPlugin( [ 'dist' ] ),
			new ExtractTextPlugin( {
				filename: '../css/[name].css',
				disable: false,
				allChunks: true,
			} ),
		],
		output: {
			path: path.resolve( __dirname, 'dist/js' ),
			filename: '[name].js',
		},
		module: {
			rules: [
				{
					test: /\.js$/,
					exclude: [
						/node_modules/,
					],
					use: [
						{
							loader: 'babel-loader',
						},
					],
				},
				{
					test: /\.scss$/,
					exclude: [
						/node_modules/,
					],
					use: ExtractTextPlugin.extract( {
						fallback: 'style-loader',
						use: [
							{
								loader: 'css-loader',
							},
							{
								loader: 'postcss-loader',
								options: {
									config: {
										ctx: {
											mode: mode,
										},
									},
								},
							},
							{
								loader: 'resolve-url-loader',
							},
							{
								loader: 'sass-loader',
								options: {
									sourceMap: true,
								},
							},
						],
					} ),
				},
			],
		},
	};
};
