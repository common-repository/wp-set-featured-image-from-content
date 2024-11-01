<?php
/**
 * Controll notifications
 *
 * @since      1.0.1
 *
 * @package    WP_Set_Featured_Image_From_Content
 * @subpackage WP_Set_Featured_Image_From_Content/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    WP_Set_Featured_Image_From_Content
 * @subpackage WP_Set_Featured_Image_From_Content/admin
 * @author     Mark Jansen (YourMark) <mark@yourmark.nl>
 */
namespace YourMark\WPSFIFC\admin;

class Notices {

	/**
	 * @var array Array that holds the notices.
	 */
	public $notices;

	/**
	 * Constructor class
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Display the notices.
	 */
	public function admin_notices() {
		if($this->notices) {
			foreach ( $this->notices as $notice ) { ?>
				<div class="notice notice-<?php echo $notice['type']; ?> is-dismissible">
					<p><?php echo $notice['message']; ?></p>
				</div> <?php
			}
		}
	}
}
?>