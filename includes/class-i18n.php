<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Set_Featured_Image_From_Content
 * @subpackage WP_Set_Featured_Image_From_Content/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    WP_Set_Featured_Image_From_Content
 * @subpackage WP_Set_Featured_Image_From_Content/includes
 * @author     Mark Jansen (YourMark) <mark@yourmark.nl>
 */
namespace YourMark\WPSFIFC\inc;

class i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-set-featured-image-from-content',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
