<?php
/**
 * Helper Mini
 *
 * @package       HELPERMINI
 * @author        Dirga
 * @license       gplv2-or-later
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   Helper Mini
 * Plugin URI:    https://mydomain.com
 * Description:   Small helper for admin
 * Version:       1.0.0
 * Author:        Dirga
 * Author URI:    https://github.com/Dirga36
 * Text Domain:   helper-mini
 * Domain Path:   /languages
 * License:       GPLv2 or later
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with Helper Mini. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
// Plugin name
define( 'HELPERMINI_NAME',			'Helper Mini' );

// Plugin version
define( 'HELPERMINI_VERSION',		'1.0.0' );

// Plugin Root File
define( 'HELPERMINI_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'HELPERMINI_PLUGIN_BASE',	plugin_basename( HELPERMINI_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'HELPERMINI_PLUGIN_DIR',	plugin_dir_path( HELPERMINI_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'HELPERMINI_PLUGIN_URL',	plugin_dir_url( HELPERMINI_PLUGIN_FILE ) );

/**
 * Load the main class for the core functionality
 */
require_once HELPERMINI_PLUGIN_DIR . 'core/class-helper-mini.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  Dirga
 * @since   1.0.0
 * @return  object|Helper_Mini
 */
function HELPERMINI() {
	return Helper_Mini::instance();
}

HELPERMINI();
