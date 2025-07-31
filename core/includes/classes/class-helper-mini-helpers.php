<?php

// Exit if accessed directly.
if (! defined('ABSPATH')) exit;

/**
 * Class Helper_Mini_Helpers
 *
 * This class contains repetitive functions that
 * are used globally within the plugin.
 *
 * @package		HELPERMINI
 * @subpackage	Classes/Helper_Mini_Helpers
 * @author		Dirga
 * @since		1.0.0
 */
class Helper_Mini_Helpers
{

	/**
	 * ######################
	 * ###
	 * #### CALLABLE FUNCTIONS
	 * ###
	 * ######################
	 */

	/**
	 * Add custom columns to the network sites table.
	 */
	public static function add_network_sites_columns($columns)
	{
		$columns['post_count'] = __('Posts', 'helper-mini');
		$columns['page_count'] = __('Pages', 'helper-mini');
		$columns['users_list'] = __('Users', 'helper-mini');
		return $columns;
	}

	/**
	 * Render custom columns content for the network sites table.
	 */
	public static function render_network_sites_custom_column($column_name, $blog_id)
	{
		switch ($column_name) {
			case 'post_count':
				switch_to_blog($blog_id);
				$count = wp_count_posts('post');
				echo intval($count->publish);
				restore_current_blog();
				break;
			case 'page_count':
				switch_to_blog($blog_id);
				$count = wp_count_posts('page');
				echo intval($count->publish);
				restore_current_blog();
				break;
			case 'users_list':
				$users = get_users(array('blog_id' => $blog_id, 'fields' => array('display_name')));
				$names = wp_list_pluck($users, 'display_name');
				echo esc_html(implode(', ', $names));
				break;
		}
	}

	/**
	 * Add custom Bulk action: "deactivate" to the network sites table.
	 */
	public static function add_network_sites_bulk_actions($actions)
	{
		$actions['deactivate_blog'] = __('Deactivate', 'helper-mini');
		return $actions;
	}

	/**
	 * Handle the custom Bulk action: "deactivate" for network sites.
	 */
	public static function handle_network_sites_bulk_action($redirect_to, $doaction, $site_ids)
	{
		if ($doaction !== 'deactivate_blog') {
			return $redirect_to;
		}

		if (! is_array($site_ids)) {
			$site_ids = array($site_ids);
		}

		foreach ($site_ids as $site_id) {
			if (get_network()->site_id != $site_id) { // Prevent deactivating main site
				update_blog_status($site_id, 'deleted', '1');
			}
		}

		$redirect_to = add_query_arg('bulk_deactivated', count($site_ids), $redirect_to);
		return $redirect_to;
	}
}
