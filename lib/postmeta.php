<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbPostMeta' ) ) {

	class ngfbPostMeta {

		protected $ngfb;	// ngfbPlugin

		// executed by ngfbPostMetaPro() as well
		// children executing this __construct() should have an empty add_actions() method
		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
			$this->add_actions();
		}

		protected function add_actions() {
			if ( is_admin() ) {
				add_action( 'add_meta_boxes', array( &$this, 'add_metaboxes' ) );
			}
		}

		public function add_metaboxes() {
			foreach ( array( 'post' => 'Post', 'page' => 'Page' ) as $id => $name ) 
				// since wp 2.5
				add_meta_box( NGFB_META_NAME, $this->ngfb->fullname . ' - Custom ' . $name . ' Settings', 
					array( &$this->ngfb->meta, 'show_metabox' ), $id, 'advanced', 'high' );
		}

		public function show_metabox( $post ) {
			$name = $post->post_type == 'page' ? 'Page' : 'Post';
			echo '<table class="ngfb-settings">';
			foreach ( $this->get_rows( $post, $name ) as $row )
				echo '<tr>' . $row . '</tr>';
			echo '</table>';
		}

		protected function get_rows( $post, $name ) {
			return array(
				'<td colspan="2" align="center">' . $this->ngfb->msgs['pro_feature'] . '</td>',

				'<th class="short">Topic</th><td class="blank">
				<p>A custom Topic for this ' . $name . ', different from the default Website Topic chosen in the <a href="' . 
				$this->ngfb->util->get_admin_url( 'webpage' ) . '">General Settings</a> 
				(currently set at "' . $this->ngfb->options['og_art_section'] . '").</p></td>',

				'<th class="short">Title</th><td class="blank">
				<p>A custom Title for this ' . $name . ' to use in the Open Graph meta tags.</td>',

				'<th class="short">Description</th><td class="blank">
				<p>A custom Description for this ' . $name . ' to use in the Open Graph meta tags.</td>',

				'<th class="short">Image ID</th><td class="blank">
				<p>A custom Image ID to use in the Open Graph meta tags, that is not already associated with or included in the content 
				of this ' . $name . ' (featured, attached, singlepic shortcode, img html tag, etc.).</p></td>',

				'<th class="short">Image URL</th><td class="blank">
				<p>You may also enter a custom image URL, instead of an Image ID, to use in the Open Graph meta tags.</p></td>',

				'<th class="short">Maximum Images</th><td class="blank">
				<p>The maximum number of images to include in the Open Graph meta tags for this ' . $name . '. 
				Select a value of "1" if you are using a custom Image ID or URL, and would like that to be the only image listed in the 
				Open Graph meta tags (the current website Maximum Images value is "' . $this->ngfb->options['og_img_max'] . '").</p></td>', 

				'<th class="short">Maximum Videos</th><td class="blank">
				<p>The maximum number of embedded videos to include in the Open Graph meta tags (the current website Maximum Videos value is 
				"' . $this->ngfb->options['og_vid_max'] . '").</p></td>',

				'<th class="short">Disable Social Buttons</th><td class="blank">
				<p>Disable all social sharing buttons for this ' . $name . ' (added to the content, excerpt, as a widget, etc.)</p></td>',
			);
		}

		public function get_options( $post_id, $idx = '' ) {
			if ( ! empty( $idx ) ) return false;
			else return array();
		}

		public function get_defaults( $idx = '' ) {
			$defs = array(
				'og_art_section' => -1,
				'og_title' => '',
				'og_desc' => '',
				'og_img_id' => '',
				'og_img_id_pre' => ( empty( $this->ngfb->options['og_def_img_id_pre'] ) ? '' : $this->ngfb->options['og_def_img_id_pre'] ),
				'og_img_url' => '',
				'og_img_max' => -1,
				'og_vid_max' => -1,
				'buttons_disabled' => 0,
			);
			if ( ! empty( $idx ) ) return $defs[$idx];
			else return $defs;
		}

		public function save_options( $post_id ) {
		}

	}
}

?>
