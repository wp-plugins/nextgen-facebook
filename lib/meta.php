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

if ( ! class_exists( 'ngfbMeta' ) ) {

	class ngfbMeta {

		protected $ngfb;		// ngfbPlugin
		protected $form;		// ngfbForm

		public function __construct( &$ngfb_plugin ) {

			$this->ngfb =& $ngfb_plugin;
			$this->enable();
		}

		public function enable() {
			if ( is_admin() ) {
				add_action( 'add_meta_boxes', array( &$this, 'add_metabox' ) );
				add_action( 'save_post', array( &$this, 'save_options' ) );
			}
		}

		public function add_metabox() {
			foreach ( array( 'post' => 'Post', 'page' => 'Page' ) as $id => $name ) 
				add_meta_box( NGFB_SHORTNAME . '_meta', 
					NGFB_FULLNAME . ' - Custom ' . $name . ' Settings', 
					array( &$this, 'show_metabox' ), $id, 'advanced', 'low' );
		}

		public function show_metabox( $post ) {
			$this->ngfb->admin->admin_style();
			echo '<table class="ngfb-settings">';
			foreach ( $this->get_rows() as $row )
				echo '<tr>' . $row . '</tr>';
			echo '</table>';
		}

		protected function get_rows() {
			return array(
				'<th>Topic</th><td>' . $this->ngfb->pro_msg . '</td>',
				'<th>Title</th><td>' . $this->ngfb->pro_msg . '</td>',
				'<th>Description</th><td>' . $this->ngfb->pro_msg . '</td>',
				'<th>Image ID</th><td>' . $this->ngfb->pro_msg . '</td>',
				'<th>Image URL</th><td>' . $this->ngfb->pro_msg . '</td>',
				'<th>Maximum Images</th><td>' . $this->ngfb->pro_msg . '</td>',
				'<th>Maximum Videos</th><td>' . $this->ngfb->pro_msg . '</td>',
				'<th>Disable Social Buttons</th><td>' . $this->ngfb->pro_msg . '</td>',
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
			return;
		}
	}
}

?>
