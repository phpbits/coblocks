<?php
/**
 * Blocks Initializer
 *
 * @package   @@pkg.title
 * @author    @@pkg.author
 * @license   @@pkg.license
 */

/**
 * Main CoBlocks_Blocks Block
 *
 * @since 1.0.0
 */
class CoBlocks_Blocks {

	/**
	 * This plugin's instance.
	 *
	 * @var CoBlocks_Blocks
	 */
	private static $instance;

	/**
	 * Registers the plugin.
	 */
	public static function register() {
		if ( null === self::$instance ) {
			self::$instance = new CoBlocks_Blocks();
		}
	}

	/**
	 * The base directory path (without trailing slash).
	 *
	 * @var string $_url
	 */
	private $_dir;

	/**
	 * The base URL path (without trailing slash).
	 *
	 * @var string $_url
	 */
	private $_url;

	/**
	 * The Plugin version.
	 *
	 * @var string $_version
	 */
	private $_version;

	/**
	 * The Constructor.
	 */
	private function __construct() {

		$this->_version = '@@pkg.version';
		$this->_slug    = 'coblocks-blocks';
		$this->_dir     = untrailingslashit( plugin_dir_path( '/', dirname( __FILE__ ) ) );
		$this->_url     = untrailingslashit( plugins_url( '/', dirname( __FILE__ ) ) );

		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'init', array( $this, 'block_assets' ) );
		add_action( 'init', array( $this, 'editor_assets' ) );
		add_action( 'plugins_loaded', array( $this, 'load_dynamic_blocks' ) );

	}

	/**
	 * Add actions to enqueue assets.
	 *
	 * @access public
	 */
	public function register_blocks() {
		register_block_type(
			'coblocks/spacer', array(
				'editor_script' => $this->_slug . '-editor',
				'editor_style'  => $this->_slug . '-editor',
				'style'         => $this->_slug . '-frontend',
			)
		);
	}

	/**
	 * Register server-side code for individual blocks.
	 *
	 * @access public
	 */
	public function load_dynamic_blocks() {
		foreach ( glob( dirname( __FILE__ ) . '/*/index.php' ) as $block_logic ) {
			require $block_logic;
		}
	}

	/**
	 * Enqueue block assets for use within Gutenberg.
	 *
	 * @access public
	 */
	public function block_assets() {

		// Styles.
		wp_register_style(
			$this->_slug . '-frontend',
			$this->_url . '/dist/blocks.style.build.css',
			array( 'wp-edit-blocks' ),
			$this->_version
		);
	}

	/**
	 * Enqueue block assets for use within Gutenberg.
	 *
	 * @access public
	 */
	public function editor_assets() {

		// Styles.
		wp_register_style(
			$this->_slug . '-editor',
			$this->_url . '/dist/blocks.editor.build.css',
			array( 'wp-edit-blocks' ),
			$this->_version
		);

		// Scripts.
		wp_register_script(
			$this->_slug . '-editor',
			$this->_url . '/dist/blocks.build.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
			$this->_version
		);
	}
}

CoBlocks_Blocks::register();
