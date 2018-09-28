module.exports = ( { file, options } ) => {
	let plugins = {
		autoprefixer: {
			browsers: [ 'last 2 versions', 'iOS >= 8' ],
		},
	};

	if ( options.mode === 'production' ) {
		plugins = Object.assign( plugins, {
			cssnano: {},
			'css-mqpacker': {},
		} );
	}

	return { plugins };
};
