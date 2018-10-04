<?php
/**
 * Partners Banner Front.
 *
 * @since   1.0.0
 * @package Partners_Banner
 */

/**
 * Partners Banner Front.
 *
 * @since 1.0.0
 */
class PB_Front {
	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 *
	 * @var   Partners_Banner
	 */
	protected $plugin = null;

	/**
	 * @var Twig_Loader_Filesystem
	 */
	protected $loader;

	/**
	 * @var Twig_Environment
	 */
	protected $twig;

	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 *
	 * @param  Partners_Banner $plugin Main plugin object.
	 *
	 * @throws Twig_Error_Loader
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();

		$this->loader = new Twig_Loader_Filesystem();

		$locations = apply_filters( 'partners_banner_views_path', [ Partners_Banner::dir( 'views' ) ] );
		if ( is_array( $locations ) && ! empty( $locations ) ) {
			foreach ( $locations as $location ) {
				if ( ! is_string( $location ) ) {
					continue;
				}

				$this->loader->addPath( $location );
			}
		}

		$this->twig = new Twig_Environment( $this->loader );
		$this->twig->addExtension( new Twig_Extension_StringLoader() );
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0.0
	 */
	public function hooks() {
		add_action( 'init', [ $this, 'shortcode' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts_and_styles' ] );
	}

	public function shortcode() {
		add_shortcode( 'partners', [ $this, 'display' ] );
	}

	public function enqueue_scripts_and_styles() {
		if ( is_admin() ) {
			return;
		}

		wp_enqueue_script(
			'slick',
			'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js',
			[ 'jquery' ],
			'1.8.1',
			true
		);

		wp_enqueue_style(
			'slick',
			'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css',
			[],
			'1.8.1'
		);

		wp_enqueue_style(
			'slick-theme',
			'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css',
			[],
			'1.8.1'
		);

		wp_enqueue_script(
			'partners-banner',
			Partners_Banner::url( 'dist/js/app.js' ),
			[ 'jquery', 'slick' ],
			substr( sha1( filemtime( Partners_Banner::dir( 'dist/js/app.js' ) ) ), 0, 8 ),
			true
		);

		wp_enqueue_style(
			'partners-banner',
			Partners_Banner::url( 'dist/css/app.css' ),
			[ 'slick', 'slick-theme' ],
			substr( sha1( filemtime( Partners_Banner::dir( 'dist/css/app.css' ) ) ), 0, 8 )
		);
	}

	public function render( $attr = [] ) {
		$attr = shortcode_atts( [
			'layout'  => $this->plugin->settings->get_value( 'layout', 'simple-list' ),
			'options' => [
				'simple_list_limit'              => intval( $this->plugin->settings->get_value( 'simple_list_limit', '' ) ),
				'carousel_slides_to_show'        => intval( $this->plugin->settings->get_value( 'carousel_slides_to_show',5 ) ),
				'carousel_slides_to_show_tablet' => intval( $this->plugin->settings->get_value( 'carousel_slides_to_show_tablet', 3 ) ),
				'carousel_slides_to_show_mobile' => intval( $this->plugin->settings->get_value( 'carousel_slides_to_show_mobile', 2 ) ),
				'carousel_speed'                 => intval( $this->plugin->settings->get_value( 'carousel_speed', 300 ) ),
				'carousel_autoplay_speed'        => intval( $this->plugin->settings->get_value( 'carousel_autoplay_speed', 3000 ) ),
				'random_layout'                  => $this->plugin->settings->get_value( 'random_layout', '4,3' ),
				'random_speed'                   => intval( $this->plugin->settings->get_value( 'random_speed', 300 ) ),
				'random_autoplay_speed'          => intval( $this->plugin->settings->get_value( 'random_autoplay_speed', 3000 ) ),
			],
		], $attr );

		if ( ! empty( $attr['options']['random_layout'] ) ) {
			$attr['options']['random_layout'] = array_map(
				'absint',
				explode( ',', $attr['options']['random_layout'] )
			);
		}

		$args = [
			'post_type'      => $this->plugin->partner->post_type(),
			'posts_per_page' => - 1,
		];

		if ($attr['layout'] === 'simple-list') {
			$limit = $attr['options']['simple_list_limit'];

			if ( ! empty( $limit ) ) {
				$args[ 'posts_per_page' ] = $limit;
			}
		}

		$partners = get_posts( $args );

		foreach ( $partners as $partner ) {
			$partner->website = get_post_meta( $partner->ID, 'partner_website', true );

			$partner->thumbnail = false;
			if ( has_post_thumbnail( $partner ) ) {
				$thumbnail_id = get_post_thumbnail_id( $partner );

				$src        = wp_get_attachment_image_src( $thumbnail_id, 'partner-thumbnail' );
				$src_retina = wp_get_attachment_image_src( $thumbnail_id, 'partner-thumbnail-retina' );

				$partner->thumbnail = [
					'src'    => ( ! empty( $src ) && is_array( $src ) ) ? $src[0] : false,
					'src@2x' => ( ! empty( $src_retina ) && is_array( $src_retina ) ) ? $src_retina[0] : false,
					'alt'    => trim( strip_tags( get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ) ),
				];
			}
		}

		$template = $this->twig->load( "{$attr['layout']}.twig" );

		return $template->render( [
			'partners' => $partners,
			'layout'   => $attr['layout'],
			'options'  => $attr['options'],
		] );
	}

	public function display( $attr = [] ) {
		echo $this->render( $attr );
	}
}
