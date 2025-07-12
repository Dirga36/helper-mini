<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'Helper_Mini' ) ) :

	/**
	 * Main Helper_Mini Class.
	 *
	 * @package		HELPERMINI
	 * @subpackage	Classes/Helper_Mini
	 * @since		1.0.0
	 * @author		Dirga
	 */
	final class Helper_Mini {

		/**
		 * The real instance
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|Helper_Mini
		 */
		private static $instance;

		/**
		 * HELPERMINI helpers object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Helper_Mini_Helpers
		 */
		public $helpers;

		/**
		 * HELPERMINI settings object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Helper_Mini_Settings
		 */
		public $settings;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to clone this class.', 'helper-mini' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to unserialize this class.', 'helper-mini' ), '1.0.0' );
		}

		/**
		 * Main Helper_Mini Instance.
		 *
		 * Insures that only one instance of Helper_Mini exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access		public
		 * @since		1.0.0
		 * @static
		 * @return		object|Helper_Mini	The one true Helper_Mini
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Helper_Mini ) ) {
				self::$instance					= new Helper_Mini;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers		= new Helper_Mini_Helpers();
				self::$instance->settings		= new Helper_Mini_Settings();

				//Fire the plugin logic
				new Helper_Mini_Run();

				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action( 'HELPERMINI/plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function includes() {
			require_once HELPERMINI_PLUGIN_DIR . 'core/includes/classes/class-helper-mini-helpers.php';
			require_once HELPERMINI_PLUGIN_DIR . 'core/includes/classes/class-helper-mini-settings.php';

			require_once HELPERMINI_PLUGIN_DIR . 'core/includes/classes/class-helper-mini-run.php';
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'helper-mini', FALSE, dirname( plugin_basename( HELPERMINI_PLUGIN_FILE ) ) . '/languages/' );
		}

	}

endif; // End if class_exists check.