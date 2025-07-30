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
}
