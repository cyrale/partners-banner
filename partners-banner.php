<?php
/**
 * Plugin Name: Partners Banner
 * Plugin URI:  https://github.com/cyrale/partners-banner
 * Description: Display a banner of your partners.
 * Version:     1.0.0
 * Author:      Cyrale
 * Author URI:  https://github.com/cyrale
 * Donate link: https://github.com/cyrale/partners-banner
 * License:     GPL2
 * Text Domain: partners-banner
 * Domain Path: /languages
 *
 * @link    https://github.com/cyrale/partners-banner
 *
 * @package Partners_Banner
 * @version 1.0.0
 *
 * Built using generator-plugin-wp (https://github.com/WebDevStudios/generator-plugin-wp)
 */

/**
 * Copyright (c) 2018 Cyril Jacquesson (email : cyril@jacquesson.me)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Use composer autoload.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Autoloads files with classes when needed.
 *
 * @since  1.0.0
 *
 * @param  string $class_name Name of the class being requested.
 */
function partners_banner_autoload_classes( $class_name ) {

	// If our class doesn't have our prefix, don't load it.
	if ( 0 !== strpos( $class_name, 'PB_' ) ) {
		return;
	}

	// Set up our filename.
	$filename = strtolower( str_replace( '_', '-', substr( $class_name, strlen( 'PB_' ) ) ) );

	// Include our file.
	Partners_Banner::include_file( 'includes/class-' . $filename );
}

spl_autoload_register( 'partners_banner_autoload_classes' );

/**
 * Main initiation class.
 *
 * @since  1.0.0
 */
final class Partners_Banner {

	/**
	 * Current version.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	const VERSION = '1.0.0';

	/**
	 * URL of plugin directory.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $path = '';

	/**
	 * Plugin basename.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $basename = '';

	/**
	 * Detailed activation error messages.
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $activation_errors = [];

	/**
	 * Singleton instance of plugin.
	 *
	 * @var    Partners_Banner
	 * @since  1.0.0
	 */
	protected static $single_instance = null;

	/**
	 * Instance of PB_Partner
	 *
	 * @since1.0.0
	 * @var PB_Partner
	 */
	protected $partner;

	/**
	 * Instance of PB_Settings
	 *
	 * @since1.0.0
	 * @var PB_Settings
	 */
	protected $settings;

	/**
	 * Instance of PB_Front
	 *
	 * @since1.0.0
	 * @var PB_Front
	 */
	protected $front;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since   1.0.0
	 * @return  Partners_Banner A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin.
	 *
	 * @since  1.0.0
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  1.0.0
	 */
	public function plugin_classes() {

		$this->partner = new PB_Partner( $this );
		$this->settings = new PB_Settings( $this );
		$this->front = new PB_Front( $this );
	} // END OF PLUGIN CLASSES FUNCTION

	/**
	 * Add hooks and filters.
	 * Priority needs to be
	 * < 10 for CPT_Core,
	 * < 5 for Taxonomy_Core,
	 * and 0 for Widgets because widgets_init runs at init priority 1.
	 *
	 * @since  1.0.0
	 */
	public function hooks() {
		add_action( 'init', [ $this, 'init' ], 0 );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_styles' ] );
	}

	/**
	 * Activate the plugin.
	 *
	 * @since  1.0.0
	 */
	public function _activate() {
		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}
	}

	/**
	 * Deactivate the plugin.
	 * Uninstall routines should be in uninstall.php.
	 *
	 * @since  1.0.0
	 */
	public function _deactivate() {
		// Add deactivation cleanup functionality here.
	}

	/**
	 * Init hooks
	 *
	 * @since  1.0.0
	 */
	public function init() {

		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		// Load translated strings for plugin.
		load_plugin_textdomain( 'partners-banner', false, dirname( $this->basename ) . '/languages/' );

		// Initialize plugin classes.
		$this->plugin_classes();
	}

	/**
	 * admin_enqueue_scripts hooks.
	 *
	 * @since 1.0.0
	 */
	public function admin_enqueue_styles() {
		wp_enqueue_style(
			'partners-banner',
			self::url( 'dist/css/admin.css' ),
			[],
			substr(sha1( filemtime( self::dir( 'dist/css/admin.css' ) ) ), 0, 8 )
		);
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  1.0.0
	 *
	 * @return boolean True if requirements met, false if not.
	 */
	public function check_requirements() {

		// Bail early if plugin meets requirements.
		if ( $this->meets_requirements() ) {
			return true;
		}

		// Add a dashboard notice.
		add_action( 'all_admin_notices', [ $this, 'requirements_not_met_notice' ] );

		// Deactivate our plugin.
		add_action( 'admin_init', [ $this, 'deactivate_me' ] );

		// Didn't meet the requirements.
		return false;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  1.0.0
	 */
	public function deactivate_me() {

		// We do a check for deactivate_plugins before calling it, to protect
		// any developers from accidentally calling it too early and breaking things.
		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( $this->basename );
		}
	}

	/**
	 * Check that all plugin requirements are met.
	 *
	 * @since  1.0.0
	 *
	 * @return boolean True if requirements are met.
	 */
	public function meets_requirements() {

		// Do checks for required classes / functions or similar.
		// Add detailed messages to $this->activation_errors array.
		return true;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met.
	 *
	 * @since  1.0.0
	 */
	public function requirements_not_met_notice() {

		// Compile default message.
		$default_message = sprintf( __( 'Partners Banner is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.',
			'partners-banner' ), admin_url( 'plugins.php' ) );

		// Default details to null.
		$details = null;

		// Add details if any exist.
		if ( $this->activation_errors && is_array( $this->activation_errors ) ) {
			$details = '<small>' . implode( '</small><br /><small>', $this->activation_errors ) . '</small>';
		}

		// Output errors.
		?>
		<div id="message" class="error">
			<p><?php echo wp_kses_post( $default_message ); ?></p>
			<?php echo wp_kses_post( $details ); ?>
		</div>
		<?php
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $field Field to get.
	 *
	 * @throws Exception     Throws an exception if the field is invalid.
	 * @return mixed         Value of the field.
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
			case 'partner':
			case 'settings':
			case 'front':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}

	/**
	 * Include a file from the includes directory.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $filename Name of the file to be included.
	 *
	 * @return boolean          Result of include call.
	 */
	public static function include_file( $filename ) {
		$file = self::dir( $filename . '.php' );
		if ( file_exists( $file ) ) {
			return include_once( $file );
		}

		return false;
	}

	/**
	 * This plugin's directory.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $path (optional) appended path.
	 *
	 * @return string       Directory and path.
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );

		return $dir . $path;
	}

	/**
	 * This plugin's url.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $path (optional) appended path.
	 *
	 * @return string       URL and path.
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );

		return $url . $path;
	}

	public function render( $atts = [], $echo = true ) {
		$render = $this->front->render( $atts, $echo );

		if ( empty( $echo ) ) {
			return $render;
		}

		echo $render;
	}
}

/**
 * Grab the Partners_Banner object and return it.
 * Wrapper for Partners_Banner::get_instance().
 *
 * @since  1.0.0
 * @return Partners_Banner  Singleton instance of plugin class.
 */
function partners_banner() {
	return Partners_Banner::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', [ partners_banner(), 'hooks' ] );

// Activation and deactivation.
register_activation_hook( __FILE__, [ partners_banner(), '_activate' ] );
register_deactivation_hook( __FILE__, [ partners_banner(), '_deactivate' ] );
