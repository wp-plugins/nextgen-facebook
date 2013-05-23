<?php
/*
Copyright 2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbMeta' ) ) {

	class ngfbMeta {

		private $ngfb;		// ngfbPlugin
		private $form;		// ngfbForm
		private $defaults = array();

		public function __construct( &$ngfb_plugin ) {

			$this->ngfb =& $ngfb_plugin;
		}

		public function add_metabox() {
			foreach ( array( 'post' => 'Post', 'page' => 'Page' ) as $id => $name ) 
				add_meta_box( NGFB_SHORTNAME . '_meta', 
					NGFB_FULLNAME . ' - Custom ' . $name . ' Settings', 
					array( &$this, 'show_metabox' ), $id, 'advanced', 'low' );
		}

		public function show_metabox( $post ) {

			$this->ngfb->admin->admin_style();
			?>
			<table class="ngfb-settings">
			<tr>
				<th class="short">Topic</th>
				<td>(not available in the basic version)</td>
			</tr>
			<tr>
				<th class="short">Title</th>
				<td>(not available in the basic version)</td>
			</tr>
			<tr>
				<th class="short">Description</th>
				<td>(not available in the basic version)</td>
			</tr>
			<tr>
				<th class="short">Image ID</th>
				<td>(not available in the basic version)</td>
			</tr>
			<tr>
				<th class="short">Image URL</th>
				<td>(not available in the basic version)</td>
			</tr>
			</table>
			<?php
		}

		public function get_options( $post_id, $idx = '' ) {
			if ( ! empty( $idx ) ) return '';
			else return array();
		}

		public function get_defaults( $idx = '' ) {
			$defs = array(
				'og_art_section' => '',
				'og_title' => '',
				'og_desc' => '',
				'og_img_id' => '',
				'og_img_id_pre' => ( empty( $this->ngfb->options['og_def_img_id_pre'] ) ? '' : $this->ngfb->options['og_def_img_id_pre'] ),
				'og_img_url' => '',
			);
			if ( ! empty( $idx ) ) return $defs[$idx];
			else return $defs;
		}

		public function save_options( $post_id ) {
			return;
		}
	}
}

?>
