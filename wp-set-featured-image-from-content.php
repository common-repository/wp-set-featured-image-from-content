<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             1.0.0
 * @package           WP_Set_Featured_Image_From_Content
 *
 * @wordpress-plugin
 * Plugin Name:       Set featured image from the content
 * Description:       Get the first image from a post and set it as featured image
 * Version:           1.0.3
 * Author:            YourMark (Mark Jansen)
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-set-featured-image-from-content
 * Domain Path:       /languages
 * Minimum PHP:       5.3
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-core.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function wpsfifc_run_plugin() {

	$plugin = new \YourMark\WPSFIFC\inc\Core();
	$plugin->run();

}

wpsfifc_run_plugin();
