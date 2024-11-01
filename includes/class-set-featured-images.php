<?php

namespace YourMark\WPSFIFC\inc;

use YourMark\WPSFIFC\admin\Notices;

class Set_Featured_Images {
	/**
	 * @var mixed|void
	 */
	private $regex;
	/**
	 * @var mixed|void
	 */
	private $post_types;
	/**
	 * @var mixed|void
	 */
	private $overwrite;
	/**
	 * @var \WP_Query
	 */
	private $posts;
	/**
	 * @var Notices
	 */
	public $notices;
	/**
	 * @var array
	 */
	public $succesfull_image_set = array();

	/**
	 * Set_Featured_Images constructor.
	 */
	public function __construct() {
		$this->regex      = apply_filters( 'wpsfifc_regex', '~<img.+src=[\'"]([^\'"]+)[\'"].*>~i' );
		$this->post_types = apply_filters( 'wpsfifc_post_types', isset( $_POST['post_types'] ) ? $_POST['post_types'] : '' );
		$this->overwrite  = apply_filters( 'wpsfifc_overwrite_existing_image', isset( $_POST['wpsfifc_overwrite_existsing'] ) ? $_POST['wpsfifc_overwrite_existsing'] : '' );
		$this->notices    = new Notices();


		if ( empty( $this->post_types ) ) {
			$this->notices->notices[] = array(
				'type'    => 'warning',
				'message' => 'Please pick a post type',
			);
		}
		else {
			add_action( 'init', array( $this, 'set_featured_images' ) );
		}
	}

	/**
	 * Get all posts from the given post type
	 *
	 * @since 1.0.0
	 * @return \WP_Query
	 */
	private function get_posts() {
		$post_args = array(
			'post_type' => $this->post_types,
			'nopaginag' => true,
		);

		return new \WP_Query( $post_args );
	}

	/**
	 * Loop through the posts, find the first local image and set that image as featured image
	 *
	 * @since 1.0.0
	 */
	public function set_featured_images() {
		// Check for theme support.
		if( ! current_theme_supports( 'post-thumbnails' ) ) {
			$this->notices->notices[] = array(
				'type' => 'error',
				'message' => sprintf( __( 'Your theme does not support featured images. To add this functionality add %s to your %s', 'wp-set-featured-image-from-content' ), '<code>add_theme_support( \'post_thumbnail\' );</code>', '<code>functions.php</code>' ),
			);
			return;
		}
		$posts = $this->get_posts();
		if ( $posts->have_posts() ) {
			while ( $posts->have_posts() ) {
				$posts->the_post();

				// If the post already has a thumbnail and overwrite is disabled, skip this round.
				if ( has_post_thumbnail( get_the_ID() ) && ! $this->overwrite ) {
					continue;
				}

				$images = array();

				// Find all the images in the post and set them in $content_images
				preg_match_all( $this->regex, get_the_content(), $content_images, PREG_SET_ORDER );

				// Loop through the images
				foreach ( $content_images as $content_image ) {
					$parsed_url = parse_url( $content_image[1] );
					// Skip external images
					if ( $parsed_url['scheme'] . '://' . $parsed_url['host'] != home_url() ) {
						continue;
					}
					else {
						$images[] = $content_image[1];
						break;
					}
				}

				// If no images were found, skip this itteration and move on to the next
				if ( empty( $images ) ) {
					continue;
				}

				// Strip the W x H from the URL
				$dimensionless_image_url_string = preg_replace( '~-\d+[Xx]\d+\.~', '.', $images[0] );

				$image_id = $this->image_exists( $dimensionless_image_url_string );

				// The image has been found in the database. Now get that ID and do something with it.
				if ( ! empty( $image_id[0] ) ) {
					set_post_thumbnail( get_the_ID(), $image_id[0] );
					$this->succesfull_image_set[ get_the_ID() ] = $image_id[0];
				}
				$this->notices->notices[] = array(
					'type' => 'success',
					'message' => sprintf( __( 'Featured image for %s successfully set', 'wp-set-featured-image-from-content' ), '<a href="' . get_permalink( get_the_ID() ) . '">' . get_the_title( get_the_ID() ) . '</a>' ),
				);
			}
			wp_reset_postdata();
		}
		else {
			$this->notices->notices[] = array(
				'type' => 'error',
				'message' => __( 'No posts matching your critiria were found. Please change the settings', 'wp-set-featured-image-from-content' ),
			);
		}
	}

	/**
	 * @param $dimensionless_image_url_string
	 *
	 * @since 1.0.0
	 * @return array
	 */
	private function image_exists( $dimensionless_image_url_string ) {
		global $wpdb;
		$image_id = $wpdb->get_col( $wpdb->prepare( "
            SELECT ID
            FROM $wpdb->posts
            WHERE guid = '%s';
            ", $dimensionless_image_url_string ) );

		return $image_id;
	}
}
