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
