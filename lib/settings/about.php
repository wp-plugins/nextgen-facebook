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

if ( ! class_exists( 'ngfbSettingsAbout' ) && class_exists( 'ngfbAdmin' ) ) {

	class ngfbSettingsAbout extends ngfbAdmin {

		protected $ngfb;
		protected $menu_id;
		protected $menu_name;
		protected $pagehook;

		// executed by ngfbSettingsAbout() as well
		public function __construct( &$ngfb_plugin, $id, $name ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->lognew();
			$this->menu_id = $id;
			$this->menu_name = $name;
		}

		protected function add_meta_boxes() {
			// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
			add_meta_box( $this->pagehook . '_description', 'Description', array( &$this, 'show_metabox_description' ), $this->pagehook, 'normal' );
			add_meta_box( $this->pagehook . '_faq', 'FAQ', array( &$this, 'show_metabox_faq' ), $this->pagehook, 'normal' );
			add_meta_box( $this->pagehook . '_remaining', 'Other Notes', array( &$this, 'show_metabox_remaining' ), $this->pagehook, 'normal' );
			add_meta_box( $this->pagehook . '_changelog', 'Changelog', array( &$this, 'show_metabox_changelog' ), $this->pagehook, 'normal' );

			$this->ngfb->user->collapse_metaboxes( $this->pagehook, array( '_faq', '_remaining', '_changelog' ) );
		}

		public function show_metabox_description() {
			?>
			<table class="ngfb-settings">
			<tr><td><?php echo $this->ngfb->admin->readme['sections']['description']; ?></td></tr>
			</table>
			<?php
		}
		
		public function show_metabox_faq() {
			?>
			<table class="ngfb-settings">
			<tr><td><?php echo $this->ngfb->admin->readme['sections']['frequently_asked_questions']; ?></td></tr>
			</table>
			<?php
		}

		public function show_metabox_remaining() {
			?>
			<table class="ngfb-settings">
			<tr><td><?php echo $this->ngfb->admin->readme['remaining_content']; ?></td></tr>
			</table>
			<?php
		}

		public function show_metabox_changelog() {
			?>
			<table class="ngfb-settings">
			<tr><td><?php echo $this->ngfb->admin->readme['sections']['changelog']; ?></td></tr>
			</table>
			<?php
		}
	}
}

?>
