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
				<p>A custom Topic for this ' . $name . ', different from the default Website Topic chosen in the General Settings.</p></td>',

				'<th class="short">Title</th><td class="blank">
				<p>A custom Title for this ' . $name . ' to use in the Open Graph, and Twitter Card meta tags.</td>',

				'<th class="short">Description</th><td class="blank">
				<p>A custom Description for this ' . $name . ' to use in the Description, Open Graph, and Twitter Card meta tags.</td>',

				'<th class="short">Image ID</th><td class="blank">
				<p>A custom Image ID to use in the Open Graph and Twitter Card meta tags.</p></td>',

				'<th class="short">Image URL</th><td class="blank">
				<p>A custom image URL, instead of an Image ID, to use in the Open Graph and Twitter Card meta tags.</p></td>',

				'<th class="short">Maximum Images</th><td class="blank">
				<p>The maximum number of images to include in the Open Graph meta tags for this ' . $name . '.</p></td>', 

				'<th class="short">Maximum Videos</th><td class="blank">
				<p>The maximum number of embedded videos to include in the Open Graph meta tags for this ' . $name . '.</p></td>',

				'<th class="short">Disable Social Buttons</th><td class="blank">
				<p>Disable all social sharing buttons for this ' . $name . '.</p></td>',
			);
		}

	}
}

?>
