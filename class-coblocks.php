<?php
/**
 * Plugin Name: CoBlocks
 * Plugin URI: https://coblocks.com
 * Description: CoBlocks is a suite of professional <strong>page building content blocks</strong> for the WordPress Gutenberg block editor. Our blocks are hyper-focused on empowering makers to build beautifully rich pages in WordPress.
 * Author: GoDaddy
 * Author URI: https://www.godaddy.com
 * Version: 1.12.1
 * Text Domain: coblocks
 * Domain Path: /languages
 * Tested up to: 5.2.2
 *
 * CoBlocks is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with CoBlocks. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package CoBlocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CoBlocks' ) ) :
	/**
	 * Main CoBlocks Class.
	 *
	 * @since 1.0.0
	 */
	final class CoBlocks {
		/**
		 * This plugin's instance.
		 *
		 * @var CoBlocks
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * Main CoBlocks Instance.
		 *
		 * Insures that only one instance of CoBlocks exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 * @static
		 * @return object|CoBlocks The one true CoBlocks
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof CoBlocks ) ) {
				self::$instance = new CoBlocks();
				self::$instance->init();
				self::$instance->constants();
				self::$instance->asset_suffix();
				self::$instance->includes();
			}
			return self::$instance;
		}

		/**
		 * Throw error on object clone.
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheating huh?', 'coblocks' ), '1.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheating huh?', 'coblocks' ), '1.0' );
		}

		/**
		 * Setup plugin constants.
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function constants() {
			$this->define( 'COBLOCKS_VERSION', '1.12.1' );
			$this->define( 'COBLOCKS_HAS_PRO', false );
			$this->define( 'COBLOCKS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			$this->define( 'COBLOCKS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'COBLOCKS_PLUGIN_FILE', __FILE__ );
			$this->define( 'COBLOCKS_PLUGIN_BASE', plugin_basename( __FILE__ ) );
			$this->define( 'COBLOCKS_REVIEW_URL', 'https://wordpress.org/support/plugin/coblocks/reviews/?filter=5' );
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param  string|string $name Name of the definition.
		 * @param  string|bool   $value Default value.
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Include required files.
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function includes() {
			require_once COBLOCKS_PLUGIN_DIR . 'includes/class-coblocks-block-assets.php';
			require_once COBLOCKS_PLUGIN_DIR . 'includes/class-coblocks-register-blocks.php';
			require_once COBLOCKS_PLUGIN_DIR . 'includes/class-coblocks-generated-styles.php';
			require_once COBLOCKS_PLUGIN_DIR . 'includes/class-coblocks-body-classes.php';
			require_once COBLOCKS_PLUGIN_DIR . 'includes/class-coblocks-form.php';
			require_once COBLOCKS_PLUGIN_DIR . 'includes/class-coblocks-font-loader.php';
			require_once COBLOCKS_PLUGIN_DIR . 'includes/class-coblocks-post-meta.php';
			require_once COBLOCKS_PLUGIN_DIR . 'includes/class-coblocks-google-map-block.php';
			require_once COBLOCKS_PLUGIN_DIR . 'includes/class-coblocks-accordion-ie-support.php';
			require_once COBLOCKS_PLUGIN_DIR . 'includes/class-coblocks-block-settings.php';
			require_once COBLOCKS_PLUGIN_DIR . 'includes/get-dynamic-blocks.php';

			if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
				require_once COBLOCKS_PLUGIN_DIR . 'includes/admin/class-coblocks-getting-started-page.php';
				require_once COBLOCKS_PLUGIN_DIR . 'includes/admin/class-coblocks-action-links.php';
				require_once COBLOCKS_PLUGIN_DIR . 'includes/admin/class-coblocks-feedback.php';
				require_once COBLOCKS_PLUGIN_DIR . 'includes/admin/class-coblocks-install.php';
			}
		}

		/**
		 * Load actions
		 *
		 * @return void
		 */
		private function init() {
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 99 );
			add_action( 'enqueue_block_editor_assets', array( $this, 'block_localization' ) );
		}

		/**
		 * Change the plugin's minified or src file name, based on debug mode.
		 *
		 * @since 1.0.0
		 */
		public function asset_suffix() {

			$suffix = SCRIPT_DEBUG ? null : '.min';

			$this->define( 'COBLOCKS_ASSET_SUFFIX', $suffix );
		}

		/**
		 * If debug is on, serve unminified source assets.
		 *
		 * @since 1.0.0
		 * @param string|string $type The type of resource.
		 * @param string|string $directory Any extra directories needed.
		 */
		public function asset_source( $type = 'js', $directory = null ) {

			if ( 'js' === $type ) {
				return SCRIPT_DEBUG ? COBLOCKS_PLUGIN_URL . 'src/' . $type . '/' . $directory : COBLOCKS_PLUGIN_URL . 'dist/' . $type . '/' . $directory;
			} else {
				return COBLOCKS_PLUGIN_URL . 'dist/css/' . $directory;
			}
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'coblocks', false, dirname( plugin_basename( COBLOCKS_PLUGIN_DIR ) ) . '/languages/' );
		}

		/**
		 * Enqueue localization data for our blocks.
		 *
		 * @access public
		 */
		public function block_localization() {
			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations( 'coblocks-editor', 'coblocks' );
			}
		}

		/**
		 * Is an AMP endpoint.
		 *
		 * @return bool Whether the current response will be AMP.
		 */
		public function is_amp() {
			return function_exists( 'is_amp_endpoint' ) && is_amp_endpoint();
		}
	}
endif;

/**
 * The main function for that returns CoBlocks
 *
 * The main function responsible for returning the one true CoBlocks
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $coblocks = CoBlocks(); ?>
 *
 * @since 1.0.0
 * @return object|CoBlocks The one true CoBlocks Instance.
 */
function coblocks() {
	return CoBlocks::instance();
}

// Get the plugin running. Load on plugins_loaded action to avoid issue on multisite.
if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	add_action( 'plugins_loaded', 'coblocks', 90 );
} else {
	coblocks();
}
