module.exports = ( api ) => {
	api.cache( true );

	return {
		presets: [
			[
				'@babel/preset-react',
				{
					pragma: 'wp.element.createElement', // default pragma is React.createElement
					pragmaFrag: 'wp.element.Fragment', // default is React.Fragment
					throwIfNamespace: false, // defaults to true
				},
			],
		],
		plugins: [
			[
				'@babel/plugin-proposal-class-properties',
			],
			[
				'@babel/plugin-proposal-object-rest-spread',
				{
					useBuiltIns: true,
				},
			],
			[
				'@babel/plugin-transform-react-jsx',
				{
					pragma: 'wp.element.createElement',
				},
			],
			[
				'@babel/plugin-transform-async-to-generator',
			],
			[
				'@babel/plugin-transform-runtime',
				{
					helpers: false,
					regenerator: true,
				},
			],
		],
	};
};
