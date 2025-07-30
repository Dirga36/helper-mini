<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) exit;

/**
 * Class Helper_Mini_Run
 *
 * Thats where we bring the plugin to life
 *
 * @package		HELPERMINI
 * @subpackage	Classes/Helper_Mini_Run
 * @author		Dirga
 * @since		1.0.0
 */
class Helper_Mini_Run
{

	/**
	 * Our Helper_Mini_Run constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct()
	{
		$this->add_hooks();
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOKS
	 * ###
	 * ######################
	 */

	/**
	 * Registers all WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.0
	 * @return	void
	 */
	private function add_hooks()
	{

		add_action('admin_enqueue_scripts', array($this, 'enqueue_backend_scripts_and_styles'), 20);
		add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts_and_styles'), 20);
		add_action('heartbeat_nopriv_received', array($this, 'myplugin_receive_heartbeat'), 20, 2);
		add_action('heartbeat_received', array($this, 'myplugin_receive_heartbeat'), 20, 2);
		add_action('plugins_loaded', array($this, 'add_wp_webhooks_integrations'), 9);
		add_filter('wpwhpro/admin/settings/menu_data', array($this, 'add_main_settings_tabs'), 20);
		add_action('wpwhpro/admin/settings/menu/place_content', array($this, 'add_main_settings_content'), 20);

		// Add custom columns to network sites table (only in network admin)
		if (is_multisite() && is_network_admin()) {
			add_filter('wpmu_blogs_columns', array('Helper_Mini_Helpers', 'add_network_sites_columns'));
			add_action('manage_sites_custom_column', array('Helper_Mini_Helpers', 'render_network_sites_custom_column'), 10, 2);
		}
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOK CALLBACKS
	 * ###
	 * ######################
	 */

	/**
	 * Enqueue the backend related scripts and styles for this plugin.
	 * All of the added scripts andstyles will be available on every page within the backend.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function enqueue_backend_scripts_and_styles()
	{
		wp_enqueue_style('helpermini-backend-styles', HELPERMINI_PLUGIN_URL . 'core/includes/assets/css/backend-styles.css', array(), HELPERMINI_VERSION, 'all');

		if (! wp_script_is('heartbeat')) {
			//enqueue the Heartbeat API
			wp_enqueue_script('heartbeat');
		}

		wp_enqueue_script('helpermini-backend-scripts', HELPERMINI_PLUGIN_URL . 'core/includes/assets/js/backend-scripts.js', array(), HELPERMINI_VERSION, false);
		wp_localize_script('helpermini-backend-scripts', 'helpermini', array(
			'plugin_name'   	=> __(HELPERMINI_NAME, 'helper-mini'),
		));
	}


	/**
	 * Enqueue the frontend related scripts and styles for this plugin.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function enqueue_frontend_scripts_and_styles()
	{
		wp_enqueue_style('helpermini-frontend-styles', HELPERMINI_PLUGIN_URL . 'core/includes/assets/css/frontend-styles.css', array(), HELPERMINI_VERSION, 'all');

		if (! wp_script_is('heartbeat')) {
			//enqueue the Heartbeat API
			wp_enqueue_script('heartbeat');
		}

		wp_enqueue_script('helpermini-frontend-scripts', HELPERMINI_PLUGIN_URL . 'core/includes/assets/js/frontend-scripts.js', array(), HELPERMINI_VERSION, false);
		wp_localize_script('helpermini-frontend-scripts', 'helpermini', array(
			'demo_var'   		=> __('This is some demo text coming from the backend through a variable within javascript.', 'helper-mini'),
		));
	}


	/**
	 * The callback function for heartbeat_received
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @param	array	$response	Heartbeat response data to pass back to front end.
	 * @param	array	$data		Data received from the front end (unslashed).
	 *
	 * @return	array	$response	The adjusted heartbeat response data
	 */
	public function myplugin_receive_heartbeat($response, $data)
	{

		//If we didn't receive our data, don't send any back.
		if (empty($data['myplugin_customfield'])) {
			return $response;
		}

		// Calculate our data and pass it back. For this example, we'll hash it.
		$received_data = $data['myplugin_customfield'];

		$response['myplugin_customfield_hashed'] = sha1($received_data);

		return $response;
	}

	/**
	 * ####################
	 * ### WP Webhooks 
	 * ####################
	 */

	/*
	 * Register dynamically all integrations
	 * The integrations are available within core/includes/integrations.
	 * A new folder is considered a new integration.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function add_wp_webhooks_integrations()
	{

		// Abort if WP Webhooks is not active
		if (! function_exists('WPWHPRO')) {
			return;
		}

		$custom_integrations = array();
		$folder = HELPERMINI_PLUGIN_DIR . 'core' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'integrations';

		try {
			$custom_integrations = WPWHPRO()->helpers->get_folders($folder);
		} catch (Exception $e) {
			WPWHPRO()->helpers->log_issue($e->getTraceAsString());
		}

		if (! empty($custom_integrations)) {
			foreach ($custom_integrations as $integration) {
				$file_path = $folder . DIRECTORY_SEPARATOR . $integration . DIRECTORY_SEPARATOR . $integration . '.php';
				WPWHPRO()->integrations->register_integration(array(
					'slug' => $integration,
					'path' => $file_path,
				));
			}
		}
	}

	/*
	 * Add the setting tabs
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @param	mixed	$tabs	All available tabs
	 *
	 * @return	array	$data
	 */
	public function add_main_settings_tabs($tabs)
	{

		$tabs['demo'] = WPWHPRO()->helpers->translate('Demo', 'admin-menu');

		return $tabs;
	}

	/*
	 * Output the content of the tab
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @param	mixed	$tab	The current tab
	 *
	 * @return	void
	 */
	public function add_main_settings_content($tab)
	{

		switch ($tab) {
			case 'demo':
				echo '<div class="wpwh-container">This is some custom text for our very own demo tab.</div>';
				break;
		}
	}
}
