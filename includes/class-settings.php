<?php
/**
 * Partners Banner Settings.
 *
 * @since   1.0.0
 * @package Partners_Banner
 */

if ( ! function_exists( 'new_cmb2_box' ) ) {
	require_once dirname( __FILE__ ) . '/../vendor/cmb2/init.php';
	require_once dirname( __FILE__ ) . '/../vendor/cmb2-tabs/cmb2-tabs.php';
}

/**
 * Partners Banner Settings class.
 *
 * @since 1.0.0
 */
class PB_Settings {
	/**
	 * Parent plugin class.
	 *
	 * @var    Partners_Banner
	 * @since  1.0.0
	 */
	protected $plugin = null;

	/**
	 * Option key, and option page slug.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected static $key = 'partners_banner_settings';

	/**
	 * Options page metabox ID.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected static $metabox_id = 'partners_banner_settings_metabox';

	/**
	 * Options Page title.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $title = '';

	/**
	 * Options Page hook.
	 *
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 *
	 * @param  Partners_Banner $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();

		// Set our title.
		$this->title = esc_attr__( 'Partners Banner - Settings', 'partners-banner' );
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0.0
	 */
	public function hooks() {
		// Hook in our actions to the admin.
		add_action( 'cmb2_admin_init', [ $this, 'add_options_page_metabox' ] );
	}

	/**
	 * Add custom fields to the options page.
	 *
	 * @since  1.0.0
	 */
	public function add_options_page_metabox() {
		$layout_labels = [
			'simple-list' => esc_html__( 'Simple list', 'partners-banner' ),
			'carousel'    => esc_html__( 'Carousel', 'partners-banner' ),
			'random'      => esc_html__( 'Random display', 'partners-banner' ),
		];

		$cmb = new_cmb2_box( [
			'id'           => self::$metabox_id,
			'title'        => $this->title,
			'object_types' => [ 'options-page' ],

			/*
			 * The following parameters are specific to the options-page box
			 * Several of these parameters are passed along to add_menu_page()/add_submenu_page().
			 */

			'option_key'    => self::$key,
			// The option key and admin menu page slug.
			// 'icon_url'        => 'dashicons-palmtree', // Menu icon. Only applicable if 'parent_slug' is left empty.
			'menu_title'    => esc_html__( 'Settings', 'partners-banner' ),
			// Falls back to 'title' (above).
			'parent_slug'   => 'edit.php?post_type=pb-partner',
			// Make options page a submenu item of the themes menu.
			// 'capability'      => 'manage_options', // Cap required to view options-page.
			// 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
			// 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
			// 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
			// 'save_button'     => esc_html__( 'Save Theme Options', 'cmb2' ), // The text for the options-page save button. Defaults to 'Save'.
			'vertical_tabs' => true, // Set vertical tabs, default false
			'tabs'          => [
				[
					'id'     => 'general',
					'icon'   => 'dashicons-admin-generic',
					'title'  => esc_html__( 'General', 'partners-banner' ),
					'fields' => [
						'layout',
						'width',
						'height',
					],
				],
				[
					'id'     => 'simple-list',
					'icon'   => 'dashicons-list-view',
					'title'  => $layout_labels['simple-list'],
					'fields' => [
						'simple_list_limit',
					],
				],
				[
					'id'     => 'carousel',
					'icon'   => 'dashicons-images-alt',
					'title'  => $layout_labels['carousel'],
					'fields' => [
						'carousel_slides_to_show',
						'carousel_slides_to_show_tablet',
						'carousel_slides_to_show_mobile',
						'carousel_speed',
						'carousel_autoplay_speed',
					],
				],
				[
					'id'     => 'random',
					'icon'   => 'dashicons-screenoptions',
					'title'  => $layout_labels['random'],
					'fields' => [
						'random_layout',
						'random_speed',
						'random_autoplay_speed',
					],
				],
			]
		] );

		$cmb->add_field( [
			'name'             => __( 'Layout', 'partners-banner' ),
			'id'               => 'layout',
			'type'             => 'radio',
			'show_option_none' => false,
			'options'          => [
				'simple-list' => $layout_labels['simple-list'],
				'carousel'    => $layout_labels['carousel'],
				'random'      => $layout_labels['random'],
			],
			'default'          => 'simple-list',
		] );

		$cmb->add_field( [
			'name'             => __( 'Width of logo', 'partners-banner' ),
			'id'               => 'width',
			'type'             => 'text_small',
			'default'          => 180,
		] );

		$cmb->add_field( [
			'name'             => __( 'Height of logo', 'partners-banner' ),
			'id'               => 'height',
			'type'             => 'text_small',
			'default'          => 100,
		] );

		$cmb->add_field( [
			'name'             => __( 'Number of partners to display', 'partners-banner' ),
			'description'      => __( 'Leave empty to have no limit.', 'partners-banner' ),
			'id'               => 'simple_list_limit',
			'type'             => 'text_small',
		] );

		$cmb->add_field( [
			'name'             => __( 'Number of partners per slide', 'partners-banner' ),
			'id'               => 'carousel_slides_to_show',
			'type'             => 'text_small',
			'default'          => 5,
		] );

		$cmb->add_field( [
			'name'             => __( 'Number of partners per slide (tablet)', 'partners-banner' ),
			'id'               => 'carousel_slides_to_show_tablet',
			'type'             => 'text_small',
			'default'          => 3,
		] );

		$cmb->add_field( [
			'name'             => __( 'Number of partners per slide (mobile)', 'partners-banner' ),
			'id'               => 'carousel_slides_to_show_mobile',
			'type'             => 'text_small',
			'default'          => 2,
		] );

		$cmb->add_field( [
			'name'             => __( 'Animation speed', 'partners-banner' ),
			'id'               => 'carousel_speed',
			'type'             => 'text_small',
			'default'          => 300,
		] );

		$cmb->add_field( [
			'name'             => __( 'Autoplay speed', 'partners-banner' ),
			'id'               => 'carousel_autoplay_speed',
			'type'             => 'text_small',
			'default'          => 3000,
		] );

		$cmb->add_field( [
			'name'             => __( 'Layout', 'partners-banner' ),
			'description'      => __( 'Indicate the number of items by row. Separate each row by comma.', 'partners-banner' ),
			'id'               => 'random_layout',
			'type'             => 'text_medium',
			'default'          => '4,3',
		] );

		$cmb->add_field( [
			'name'             => __( 'Animation speed', 'partners-banner' ),
			'id'               => 'random_speed',
			'type'             => 'text_small',
			'default'          => 300,
		] );

		$cmb->add_field( [
			'name'             => __( 'Autoplay speed', 'partners-banner' ),
			'id'               => 'random_autoplay_speed',
			'type'             => 'text_small',
			'default'          => 3000,
		] );
	}

	/**
	 * Wrapper function around cmb2_get_option.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $key     Options array key
	 * @param  mixed  $default Optional default value
	 *
	 * @return mixed           Option value
	 */
	public static function get_value( $key = '', $default = false ) {
		if ( function_exists( 'cmb2_get_option' ) ) {
			// Use cmb2_get_option as it passes through some key filters.
			return cmb2_get_option( self::$key, $key, $default );
		}

		// Fallback to get_option if CMB2 is not loaded yet.
		$opts = get_option( self::$key, $default );

		$val = $default;

		if ( 'all' == $key ) {
			$val = $opts;
		} elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
			$val = $opts[ $key ];
		}

		return $val;
	}
}
