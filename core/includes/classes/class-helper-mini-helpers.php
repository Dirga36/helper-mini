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
		}
	}

	//---------------------------------------------------------------------------------------

	/**
	 * Add custom Bulk actions: "deactivate" and "advanced delete" to the network sites table.
	 */
	public static function add_network_sites_bulk_actions($actions)
	{
		$actions['deactivate_sites'] = __('Advanced deactivate', 'helper-mini');
		$actions['activate_sites'] = __('Advanced activate', 'helper-mini');
		$actions['destroy_sites'] = __('Advanced delete', 'helper-mini');
		return $actions;
	}

	/**
	 * Handle the custom Bulk actions: "deactivate" and "advanced delete" for network sites.
	 */
	public static function handle_network_sites_bulk_action($redirect_to, $doaction, $site_ids)
	{
		if (! is_array($site_ids)) {
			$site_ids = array($site_ids);
		}

		$network_main_id = get_network()->site_id;

		switch ($doaction) {
			case 'deactivate_sites':
				if (! is_array($site_ids)) {
					$site_ids = array($site_ids);
				}

				$deleted_sites = 0;

				// Loop through each site in the network, except the main site
				foreach ($site_ids as $site_id) {
					$network_main_id = get_network()->site_id;
					if ($network_main_id === $site_id) {
						continue; // Skip main site
					}

					// Move context to child site for content evaluation
					switch_to_blog($site_id);
					$post_count = wp_count_posts('post');
					$page_count = wp_count_posts('page');

					$num_posts = isset($post_count->publish) ? intval($post_count->publish) : 0;
					$num_pages = isset($page_count->publish) ? intval($page_count->publish) : 0;

					// Return context to the original blog before next action
					restore_current_blog();

					// If the site meets the requirements, mark it as "deleted"
					if ($num_posts <= 2 && $num_pages <= 2) {
						update_blog_status($site_id, 'deleted', '1');
						$deleted_sites++; // Increment count for sites that are deleted
					}
				}

				$redirect_to = add_query_arg('deactivated_sites', $deleted_sites, $redirect_to);
				return $redirect_to;
				break;
			case 'destroy_sites':
				$destroyed_sites = 0;

				foreach ($site_ids as $site_id) {
					if ($site_id == $network_main_id) continue;

					switch_to_blog($site_id);
					$post_count = wp_count_posts('post');
					$page_count = wp_count_posts('page');
					$num_posts = isset($post_count->publish) ? intval($post_count->publish) : 0;
					$num_pages = isset($page_count->publish) ? intval($page_count->publish) : 0;
					restore_current_blog();

					if ($num_posts <= 1 && $num_pages <= 1) {
						wpmu_delete_blog($site_id, true);
						$destroyed_sites++;
					}
				}

				$redirect_to = add_query_arg('destroyed_sites', $destroyed_sites, $redirect_to);
				break;
			case 'activate_sites':
				if (! is_array($site_ids)) {
					$site_ids = array($site_ids);
				}

				$activated_sites = 0;

				// Loop through each site in the network, except the main site
				foreach ($site_ids as $site_id) {
					$network_main_id = get_network()->site_id;
					if ($network_main_id === $site_id) {
						continue; // Skip main site
					}

					update_blog_status($site_id, 'deleted', '0');
					$activated_sites++; // Increment count for sites that are ativated
				}

				$redirect_to = add_query_arg('activated_sites', $activated_sites, $redirect_to);
				return $redirect_to;
				break;
		}

		return $redirect_to;
	}
}
