<?php
/*
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/

This script is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later
version.

This script is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details at
http://www.gnu.org/licenses/.
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
			$this->ngfb->debug->lognew();
			$this->add_actions();
		}

		private function add_actions() {
			if ( is_admin() ) {
				add_action( 'add_meta_boxes', array( &$this, 'add_metaboxes' ) );
				add_action( 'save_post', array( &$this, 'save_options' ) );
			}
		}

		public function add_metaboxes() {
			foreach ( array( 'post' => 'Post', 'page' => 'Page' ) as $id => $name ) 
				add_meta_box( NGFB_META_NAME, $this->ngfb->fullname . ' - Custom ' . $name . ' Settings', 
					array( &$this->ngfb->meta, 'show_metabox' ), $id, 'advanced', 'high' );
		}

		public function show_metabox( $post ) {
			$this->ngfb->admin->settings_style();
			echo '<table class="ngfb-settings">';
			foreach ( $this->get_rows() as $row )
				echo '<tr>' . $row . '</tr>';
			echo '</table>';
		}

		protected function get_rows() {
			return array(
				'<td colspan="2" align="center">' . $this->ngfb->msgs['pro_feature'] . '</td>',
				'<th>Topic</th><td class="blank" width="100%"></td>',
				'<th>Title</th><td class="blank"></td>',
				'<th>Description</th><td class="blank"></td>',
				'<th>Image ID</th><td class="blank"></td>',
				'<th>Image URL</th><td class="blank"></td>',
				'<th>Maximum Images</th><td class="blank"></td>',
				'<th>Maximum Videos</th><td class="blank"></td>',
				'<th>Disable Social Buttons</th><td class="blank"></td>',
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
			);
			if ( ! empty( $idx ) ) return $defs[$idx];
			else return $defs;
		}

		public function save_options( $post_id ) {
		}
	}
}

?>
