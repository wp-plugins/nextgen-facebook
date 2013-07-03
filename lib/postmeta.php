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
			if ( is_admin() )
				add_action( 'add_meta_boxes', array( &$this, 'add_metaboxes' ) );
		}

		public function add_metaboxes() {
			foreach ( array( 'post' => 'Post', 'page' => 'Page' ) as $id => $name ) 
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
				'<td colspan="2" align="center">' . $this->ngfb->msg->get( 'pro_feature' ) . '</td>',

				$this->ngfb->util->th( 'Topic', 'medium', null, 
					'A custom topic, different from the default Website Topic chosen in the General Settings.' ) .
				'<td class="blank">&nbsp;</td>',

				$this->ngfb->util->th( 'Title', 'medium', null, 
					'A custom title to use in the Open Graph and Twitter Card meta tags.' ) .
				'<td class="blank">&nbsp;</td>',

				$this->ngfb->util->th( 'Open Graph+ Description', 'medium', null, 
					'A custom description to use in the Open Graph meta tags, along with the Pinterest and Tumblr social sharing buttons.' ) .
				'<td class="blank">&nbsp;</td>',

				$this->ngfb->util->th( 'Google Description', 'medium', null, 
					'A custom description to use for the Google Search description meta tag.' ) .
				'<td class="blank">&nbsp;</td>',

				$this->ngfb->util->th( 'Twitter Card Description', 'medium', null, 
					'A custom description to use for the Twitter Card description meta tag.' ) .
				'<td class="blank">&nbsp;</td>',

				$this->ngfb->util->th( 'Tweet Text', 'medium', null, 
					'A custom Tweet text for the Twitter social sharing button.' ) .
				'<td class="blank">&nbsp;</td>',

				$this->ngfb->util->th( 'Image ID', 'medium', null, 
					'A custom Image ID, that is not already associated (featured, attached, singlepic shortcode, img html tag, etc.), 
					to include in the Open Graph and the \'Large Image Summary\' Twitter Card meta tags.' ) .
				'<td class="blank">&nbsp;</td>',

				$this->ngfb->util->th( 'Image URL', 'medium', null, 
					'A custom image URL, instead of an Image ID, to include in the Open Graph and Twitter Card meta tags.' ) .
				'<td class="blank">&nbsp;</td>',

				$this->ngfb->util->th( 'Video URL', 'medium', null, 
					'A custom video URL, from YouTube or Vimeo, to include in the Open Graph and Twitter Card meta tags.' ) .
				'<td class="blank">&nbsp;</td>',

				$this->ngfb->util->th( 'Maximum Images', 'medium', null, 
					'The maximum number of images to include in the Open Graph meta tags for this ' . $name . '.' ) .
				'<td class="blank">&nbsp;</td>',

				$this->ngfb->util->th( 'Maximum Videos', 'medium', null, 
					'The maximum number of embedded videos to include in the Open Graph meta tags for this ' . $name . '.' ) .
				'<td class="blank">&nbsp;</td>',

				$this->ngfb->util->th( 'Disable Social Buttons', 'medium', null, 
					'Disable all social sharing buttons (content, excerpt, widget, shortcode) for this ' . $name . '.' ) .
				'<td class="blank">&nbsp;</td>',
			);
		}

                public function get_options( $post_id, $idx = '' ) {
			if ( ! empty( $idx ) ) return false;
			else return array();
		}

	}
}

?>
