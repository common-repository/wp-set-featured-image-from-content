<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Set_Featured_Image_From_Content
 * @subpackage WP_Set_Featured_Image_From_Content/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Set_Featured_Image_From_Content
 * @subpackage WP_Set_Featured_Image_From_Content/admin
 * @author     Mark Jansen (YourMark) <mark@yourmark.nl>
 */
namespace YourMark\WPSFIFC\admin;

use YourMark\WPSFIFC\inc\Set_Featured_Images;

class Core {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Capability needed to use this plugin
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $capability
	 */
	private $capability;

	/**
	 * Holds instance of Set Featured Image class
	 *
	 * @since  1.0.1
	 * @access private
	 * @var object Set_Featured_Images
	 */
	private $featured_images;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->capability  = apply_filters( 'wp_sfifc_cap', 'manage_options' );

		if ( ! empty( $_POST['wp-set-featured-images-from-content'] ) ) {
			$this->featured_images = new Set_Featured_Images();
		}

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.1
	 */
	public function enqueue_styles( $hook ) {
		if ( 'tools_page_wp-set-featured-image-from-content' == $hook ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/css/wpsfifc' . ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.min' : '' ) . '.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.1
	 */
	public function enqueue_scripts( $hook ) {
		if ( 'tools_page_wp-set-featured-image-from-content' == $hook ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/wpsfifc' . ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.min' : '' ) . '.js', array( 'jquery' ), $this->version, false );
		}
	}

	/**
	 * Add a submenu page
	 */
	public function admin_menu() {
		add_submenu_page(
			'tools.php',
			'WP Set Featured Image From Content',
			'WP Set Featured Image From Content',
			$this->capability,
			'wp-set-featured-image-from-content',
			array( $this, 'settings' )
		);
	}

	/**
	 * Output the HTML for the settings page.
	 */
	public function settings() { ?>
		<div class="wrap">
			<h2>WP Set Featured Image From Content</h2>
			<?php
			$this->settings_form(); ?>
		</div> <?php
	}

	/**
	 * Show settings form.
	 */
	private function settings_form() { ?>
		<p><?php _e( 'Choose your settings and press the button below to set featured images from post content', 'wp-set-featured-image-from-content' ); ?></p>
		<h2>Settings</h2>
		<form method="post" action="" class="wpsfifc-form">
			<table class="form-table">
				<tbody>
				<tr>
					<th>
						<label for="wpsfifc_overwrite_existsing"><?php _e( 'Overwrite', 'wp-set-featured-image-from-content' ); ?></label>
					</th>
					<td>
						<input name="wpsfifc_overwrite_existsing" id="wpsfifc_overwrite_existsing" type="checkbox" value="1" <?php checked('1', ( isset( $_POST['wpsfifc_overwrite_existsing'] ) ? $_POST['wpsfifc_overwrite_existsing'] : '' ) ); ?>>
						<label for="wpsfifc_overwrite_existsing"><?php _e( 'Overwrite existing featured images', 'wp-set-featured-image-from-content' ); ?></label>
					</td>
				</tr>
				<tr>
					<th>
						<label for="post_types">Post types</label>
					</th>
					<td>
						<select data-placeholder="<?php _e( 'Select one or more post types', 'wp-set-featured-image-from-content' ); ?>" id="post_types" name="post_types[]" multiple class="chosen-select" style="width: 250px;">
							<?php foreach ( $this->get_post_types() as $post_type ) {
								echo '<option value="' . $post_type->name . '">' . $post_type->label . '</option>';
							} ?>
						</select>
					</td>
				</tr>
				</tbody>
			</table>
			<input type="submit" class="button-primary hide-if-no-js" name="wp-set-featured-images-from-content" id="wp-set-featured-images-from-content" value="<?php _e( 'Set featured images', 'wp-set-featured-image-from-content' ); ?>" <?php echo ( ! current_theme_supports( 'post-thumbnails' ) ? 'disabled' : '' ); ?>>
			<?php if( ! current_theme_supports( 'post-thumbnails' ) ) {
				echo '<p class="description">' . sprintf( __( 'Your theme does not support featured images. To add this functionality add %s to your %s', 'wp-set-featured-image-from-content' ), '<code>add_theme_support( \'post_thumbnail\' );</code>', '<code>functions.php</code>' ) . '</p>';
			} ?>
			<?php echo wp_nonce_field( 'wp-set-featured-image-from-content' ); ?>
		</form> <?php
	}

	/**
	 * Get post types, and also discard a few of them.
	 *
	 * @return array Array with post types.
	 *
	 * @since 1.0.1
	 */
	private function get_post_types() {
		$post_types = get_post_types( array(), 'objects' );

		// Unset a few standard WP Post types that you never need for this anyway.
		unset( $post_types['attachment'] );
		unset( $post_types['revision'] );
		unset( $post_types['nav_menu_item'] );

		return $post_types;
	}
}
