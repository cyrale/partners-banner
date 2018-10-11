<?php
/**
 * Partners Banner Partner.
 *
 * @since   1.0.0
 * @package Partners_Banner
 */

if ( ! class_exists( 'CPT_Core' ) ) {
	require_once dirname( __FILE__ ) . '/../vendor/cpt-core/CPT_Core.php';
}

if ( ! function_exists( 'new_cmb2_box' ) ) {
	require_once dirname( __FILE__ ) . '/../vendor/cmb2/init.php';
}

/**
 * Partners Banner Partner post type class.
 *
 * @since 1.0.0
 *
 * @see   https://github.com/WebDevStudios/CPT_Core
 */
class PB_Partner extends CPT_Core {
	/**
	 * Parent plugin class.
	 *
	 * @var Partners_Banner
	 * @since  1.0.0
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * Register Custom Post Types.
	 *
	 * See documentation in CPT_Core, and in wp-includes/post.php.
	 *
	 * @since  1.0.0
	 *
	 * @param  Partners_Banner $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();

		// Register this cpt.
		// First parameter should be an array with Singular, Plural, and Registered name.
		parent::__construct(
			[
				esc_html__( 'Partner', 'partners-banner' ),
				esc_html__( 'Partners', 'partners-banner' ),
				'pb-partner',
			],
			[
				'supports'         => [
					'title',
					'thumbnail',
				],
				'menu_icon'        => 'dashicons-businessman',
				'public'           => false,
				'show_in_menu'     => true,
				'show_in_nav_menu' => false,
				'show_in_rest'     => false,
			]
		);
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0.0
	 */
	public function hooks() {
		add_action( 'cmb2_admin_init', [ $this, 'add_partner_metabox' ] );
		add_action( 'after_setup_theme', [ $this, 'image_size' ] );
	}

	/**
	 * Registers admin columns to display. Hooked in via CPT_Core.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $columns Array of registered column names/labels.
	 *
	 * @return array          Modified array.
	 */
	public function columns( $columns ) {
		$new_columns = [
			'partner-thumbnail' => esc_html__( 'Thumbnail', 'partners-banner' ),
		];

		return $this->insert_after( $columns, $new_columns, 'title' );
	}

	/**
	 * Handles admin column display. Hooked in via CPT_Core.
	 *
	 * @since  1.0.0
	 *
	 * @param array   $column  Column currently being rendered.
	 * @param integer $post_id ID of post to display column for.
	 */
	public function columns_display( $column, $post_id ) {
		switch ( $column ) {
			case 'partner-thumbnail':
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'medium' );
				}

				break;
		}
	}

	public function add_partner_metabox() {
		$cmb = new_cmb2_box(
			[
				'id'           => 'partner-options',
				'title'        => __( 'Options', 'partners-banner' ),
				'object_types' => [ $this->post_type ],
			]
		);

		$cmb->add_field(
			[
				'name' => __( 'Website', 'partners-banner' ),
				'id'   => 'partner_website',
				'type' => 'text_url',
			]
		);
	}

	/**
	 * Define new image size for the thumbnail of partner.
	 */
	public function image_size() {
		$width  = intval( $this->plugin->settings->get_value( 'width', 180 ) );
		$height = intval( $this->plugin->settings->get_value( 'height', 100 ) );

		add_image_size( 'partner-thumbnail', $width, $height );
		add_image_size( 'partner-thumbnail-retina', 2 * $width, 2 * $height );
	}

	/**
	 * Insert array in another array after a specified key.
	 *
	 * @param array  $columns     Original array of columns.
	 * @param array  $new_columns New columns to insert into original array.
	 * @param string $column_name The name of the columne to insert after.
	 *
	 * @return array
	 */
	private function insert_after( $columns, $new_columns, $column_name ) {
		$index = array_search( $column_name, array_keys( $columns ), true );

		if ( false === $index ) {
			return array_merge( $columns, $new_columns );
		}

		$columns = array_merge(
			array_slice( $columns, 0, $index + 1 ),
			$new_columns,
			array_slice( $columns, $index + 1 )
		);

		return $columns;
	}
}
